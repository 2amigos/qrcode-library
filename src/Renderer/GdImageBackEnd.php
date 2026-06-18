<?php

/*
 * This file is part of the 2amigos/qrcode-library project.
 *
 * (c) 2amigOS! <http://2am.tech/>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Da\QrCode\Renderer;

use BaconQrCode\Renderer\Color\Alpha;
use BaconQrCode\Renderer\Color\ColorInterface;
use BaconQrCode\Renderer\Image\ImageBackEndInterface;
use BaconQrCode\Renderer\Image\TransformationMatrix;
use BaconQrCode\Renderer\Path\Close;
use BaconQrCode\Renderer\Path\Curve;
use BaconQrCode\Renderer\Path\EllipticArc;
use BaconQrCode\Renderer\Path\Line;
use BaconQrCode\Renderer\Path\Move;
use BaconQrCode\Renderer\Path\Path;
use BaconQrCode\Renderer\RendererStyle\Gradient;
use BaconQrCode\Renderer\RendererStyle\GradientType;
use Da\QrCode\Exception\BadMethodCallException;
use GdImage;

/**
 * A pure GD implementation of Bacon's {@see ImageBackEndInterface}.
 *
 * Unlike {@see \BaconQrCode\Renderer\Image\ImagickImageBackEnd} this back end does NOT require the
 * `ext-imagick` extension — it relies solely on `ext-gd`, which the library already requires. It is
 * the default render back end for the PNG and JPG writers, which is what resolves the long-standing
 * Windows ImageMagick error (#68).
 *
 * Module, eye and gradient styling is preserved: the back end is driven by Bacon's
 * {@see \BaconQrCode\Renderer\ImageRenderer}, flattens curves (rounded modules) and elliptic arcs
 * (dot modules) into polygons, and fills them honouring the SVG even-odd rule.
 */
final class GdImageBackEnd implements ImageBackEndInterface
{
    /**
     * Number of straight segments used to approximate a full bezier curve / elliptic arc.
     */
    private const CURVE_STEPS = 24;

    private string $imageFormat;

    private int $compressionQuality;

    private ?GdImage $image = null;

    /**
     * Transformation matrix stack. The top of the stack maps module coordinates to pixels.
     *
     * @var TransformationMatrix[]
     */
    private array $matrices = [];

    private int $matrixIndex = 0;

    public function __construct(string $imageFormat = 'png', int $compressionQuality = 100)
    {
        $this->imageFormat = $imageFormat;
        $this->compressionQuality = $compressionQuality;
    }

    public function new(int $size, ColorInterface $backgroundColor): void
    {
        $image = imagecreatetruecolor($size, $size);

        if ($image === false) {
            throw new BadMethodCallException('Could not allocate a GD image resource');
        }

        imagealphablending($image, false);
        imagesavealpha($image, true);
        imagefilledrectangle($image, 0, 0, $size - 1, $size - 1, $this->colorIndex($image, $backgroundColor));
        // From now on, foreground modules are blended on top of the background.
        imagealphablending($image, true);

        $this->image = $image;
        $this->matrices = [new TransformationMatrix()];
        $this->matrixIndex = 0;
    }

    public function scale(float $size): void
    {
        $this->matrices[$this->matrixIndex] = $this->matrices[$this->matrixIndex]
            ->multiply(TransformationMatrix::scale($size));
    }

    public function translate(float $x, float $y): void
    {
        $this->matrices[$this->matrixIndex] = $this->matrices[$this->matrixIndex]
            ->multiply(TransformationMatrix::translate($x, $y));
    }

    public function rotate(int $degrees): void
    {
        $this->matrices[$this->matrixIndex] = $this->matrices[$this->matrixIndex]
            ->multiply(TransformationMatrix::rotate($degrees));
    }

    public function push(): void
    {
        $this->matrices[++$this->matrixIndex] = $this->matrices[$this->matrixIndex - 1];
    }

    public function pop(): void
    {
        if ($this->matrixIndex > 0) {
            unset($this->matrices[$this->matrixIndex--]);
        }
    }

    public function drawPathWithColor(Path $path, ColorInterface $color): void
    {
        $image = $this->requireImage();
        $rgba = $this->rgba($color);

        $this->fillEvenOdd(
            $this->buildSubPaths($path),
            static fn (): int => ($rgba['a'] << 24) | ($rgba['r'] << 16) | ($rgba['g'] << 8) | $rgba['b'],
            $image
        );
    }

    public function drawPathWithGradient(
        Path $path,
        Gradient $gradient,
        float $x,
        float $y,
        float $width,
        float $height
    ): void {
        $image = $this->requireImage();

        // Map the gradient box (in module coordinates) to its axis-aligned pixel bounding box.
        // All four corners are transformed so the box stays correct even under a rotated matrix.
        $matrix = $this->matrices[$this->matrixIndex];
        [$x0, $y0] = $matrix->apply($x, $y);
        [$x1, $y1] = $matrix->apply($x + $width, $y + $height);
        [$x2, $y2] = $matrix->apply($x + $width, $y);
        [$x3, $y3] = $matrix->apply($x, $y + $height);
        $left = min($x0, $x1, $x2, $x3);
        $top = min($y0, $y1, $y2, $y3);
        $boxWidth = max(max($x0, $x1, $x2, $x3) - $left, 1e-6);
        $boxHeight = max(max($y0, $y1, $y2, $y3) - $top, 1e-6);

        $start = $this->rgba($gradient->getStartColor());
        $end = $this->rgba($gradient->getEndColor());
        $type = $gradient->getType();

        $colorAt = function (int $px, int $py) use ($start, $end, $type, $left, $top, $boxWidth, $boxHeight): int {
            $t = $this->gradientRatio($type, $px, $py, $left, $top, $boxWidth, $boxHeight);
            $r = (int) round($start['r'] + ($end['r'] - $start['r']) * $t);
            $g = (int) round($start['g'] + ($end['g'] - $start['g']) * $t);
            $b = (int) round($start['b'] + ($end['b'] - $start['b']) * $t);
            $a = (int) round($start['a'] + ($end['a'] - $start['a']) * $t);

            return ($a << 24) | ($r << 16) | ($g << 8) | $b;
        };

        $this->fillEvenOdd($this->buildSubPaths($path), $colorAt, $image);
    }

    public function done(): string
    {
        $image = $this->requireImage();

        ob_start();
        if ($this->imageFormat === 'jpeg' || $this->imageFormat === 'jpg') {
            imagejpeg($image, null, $this->compressionQuality);
        } else {
            imagepng($image);
        }
        $blob = (string) ob_get_clean();

        // Note: imagedestroy() is intentionally not called — it is a no-op since PHP 8.0 and
        // deprecated since PHP 8.5. GD images are freed automatically once unreferenced.
        $this->image = null;
        $this->matrices = [];

        return $blob;
    }

    /**
     * Converts a Bacon {@see Path} into a list of polygons (closed point lists) in pixel space.
     *
     * @return array<int, array<int, float>> each entry is a flat [x0, y0, x1, y1, ...] point list
     */
    private function buildSubPaths(Path $path): array
    {
        $matrix = $this->matrices[$this->matrixIndex];
        $subPaths = [];
        $current = [];
        $startX = 0.0;
        $startY = 0.0;
        $cursorX = 0.0;
        $cursorY = 0.0;

        $push = function (float $mx, float $my) use (&$current, $matrix): void {
            [$px, $py] = $matrix->apply($mx, $my);
            $current[] = $px;
            $current[] = $py;
        };

        foreach ($path as $op) {
            switch (true) {
                case $op instanceof Move:
                    if (count($current) >= 6) {
                        $subPaths[] = $current;
                    }
                    $current = [];
                    $startX = $cursorX = $op->getX();
                    $startY = $cursorY = $op->getY();
                    $push($cursorX, $cursorY);
                    break;

                case $op instanceof Line:
                    $cursorX = $op->getX();
                    $cursorY = $op->getY();
                    $push($cursorX, $cursorY);
                    break;

                case $op instanceof Curve:
                    $this->flattenCurve(
                        $cursorX,
                        $cursorY,
                        $op->getX1(),
                        $op->getY1(),
                        $op->getX2(),
                        $op->getY2(),
                        $op->getX3(),
                        $op->getY3(),
                        $push
                    );
                    $cursorX = $op->getX3();
                    $cursorY = $op->getY3();
                    break;

                case $op instanceof EllipticArc:
                    $this->flattenArc($cursorX, $cursorY, $op, $push);
                    $cursorX = $op->getX();
                    $cursorY = $op->getY();
                    break;

                case $op instanceof Close:
                    $cursorX = $startX;
                    $cursorY = $startY;
                    break;
            }
        }

        if (count($current) >= 6) {
            $subPaths[] = $current;
        }

        return $subPaths;
    }

    /**
     * Flattens a cubic bezier curve into line segments (used by the rounded module style).
     */
    private function flattenCurve(
        float $x0,
        float $y0,
        float $x1,
        float $y1,
        float $x2,
        float $y2,
        float $x3,
        float $y3,
        callable $push
    ): void {
        for ($i = 1; $i <= self::CURVE_STEPS; $i++) {
            $t = $i / self::CURVE_STEPS;
            $mt = 1 - $t;
            $a = $mt * $mt * $mt;
            $b = 3 * $mt * $mt * $t;
            $c = 3 * $mt * $t * $t;
            $d = $t * $t * $t;
            $push(
                $a * $x0 + $b * $x1 + $c * $x2 + $d * $x3,
                $a * $y0 + $b * $y1 + $c * $y2 + $d * $y3
            );
        }
    }

    /**
     * Flattens an SVG elliptic arc into line segments (used by the dot module style).
     *
     * Implements the endpoint-to-center parameterisation from the SVG specification.
     */
    private function flattenArc(float $x0, float $y0, EllipticArc $arc, callable $push): void
    {
        $rx = abs($arc->getXRadius());
        $ry = abs($arc->getYRadius());
        $x1 = $arc->getX();
        $y1 = $arc->getY();

        if ($rx < 1e-9 || $ry < 1e-9) {
            $push($x1, $y1);

            return;
        }

        $phi = deg2rad($arc->getXAxisAngle());
        $cosPhi = cos($phi);
        $sinPhi = sin($phi);

        $dx = ($x0 - $x1) / 2;
        $dy = ($y0 - $y1) / 2;
        $x1p = $cosPhi * $dx + $sinPhi * $dy;
        $y1p = -$sinPhi * $dx + $cosPhi * $dy;

        // Correct out-of-range radii.
        $lambda = ($x1p ** 2) / ($rx ** 2) + ($y1p ** 2) / ($ry ** 2);
        if ($lambda > 1) {
            $scale = sqrt($lambda);
            $rx *= $scale;
            $ry *= $scale;
        }

        $sign = ($arc->isLargeArc() !== $arc->isSweep()) ? 1 : -1;
        $num = ($rx ** 2) * ($ry ** 2) - ($rx ** 2) * ($y1p ** 2) - ($ry ** 2) * ($x1p ** 2);
        $den = ($rx ** 2) * ($y1p ** 2) + ($ry ** 2) * ($x1p ** 2);
        $coef = $sign * sqrt(max(0.0, $num / $den));

        $cxp = $coef * ($rx * $y1p) / $ry;
        $cyp = $coef * -($ry * $x1p) / $rx;

        $cx = $cosPhi * $cxp - $sinPhi * $cyp + ($x0 + $x1) / 2;
        $cy = $sinPhi * $cxp + $cosPhi * $cyp + ($y0 + $y1) / 2;

        $theta1 = $this->angle(1.0, 0.0, ($x1p - $cxp) / $rx, ($y1p - $cyp) / $ry);
        $deltaTheta = $this->angle(
            ($x1p - $cxp) / $rx,
            ($y1p - $cyp) / $ry,
            (-$x1p - $cxp) / $rx,
            (-$y1p - $cyp) / $ry
        );

        if (! $arc->isSweep() && $deltaTheta > 0) {
            $deltaTheta -= 2 * M_PI;
        } elseif ($arc->isSweep() && $deltaTheta < 0) {
            $deltaTheta += 2 * M_PI;
        }

        for ($i = 1; $i <= self::CURVE_STEPS; $i++) {
            $theta = $theta1 + $deltaTheta * ($i / self::CURVE_STEPS);
            $ex = $cx + $rx * cos($theta) * $cosPhi - $ry * sin($theta) * $sinPhi;
            $ey = $cy + $rx * cos($theta) * $sinPhi + $ry * sin($theta) * $cosPhi;
            $push($ex, $ey);
        }
    }

    private function angle(float $ux, float $uy, float $vx, float $vy): float
    {
        $dot = $ux * $vx + $uy * $vy;
        $len = sqrt(($ux ** 2 + $uy ** 2) * ($vx ** 2 + $vy ** 2));
        $value = $len < 1e-12 ? 0.0 : max(-1.0, min(1.0, $dot / $len));
        $angle = acos($value);

        return ($ux * $vy - $uy * $vx) < 0 ? -$angle : $angle;
    }

    /**
     * Even-odd scanline fill across all sub-paths of a single Bacon path.
     *
     * @param array<int, array<int, float>> $subPaths
     */
    private function fillEvenOdd(array $subPaths, callable $colorAt, GdImage $image): void
    {
        if ($subPaths === []) {
            return;
        }

        $minY = INF;
        $maxY = -INF;
        foreach ($subPaths as $points) {
            for ($i = 1, $n = count($points); $i < $n; $i += 2) {
                $minY = min($minY, $points[$i]);
                $maxY = max($maxY, $points[$i]);
            }
        }

        $imageHeight = imagesy($image);
        $firstRow = max(0, (int) floor($minY));
        $lastRow = min($imageHeight - 1, (int) ceil($maxY));

        for ($row = $firstRow; $row <= $lastRow; $row++) {
            $scanY = $row + 0.5;
            $crossings = [];

            foreach ($subPaths as $points) {
                $count = count($points) / 2;
                for ($i = 0; $i < $count; $i++) {
                    $ax = $points[2 * $i];
                    $ay = $points[2 * $i + 1];
                    $j = ($i + 1) % $count;
                    $bx = $points[2 * $j];
                    $by = $points[2 * $j + 1];

                    if (($ay <= $scanY && $by > $scanY) || ($by <= $scanY && $ay > $scanY)) {
                        $crossings[] = $ax + ($scanY - $ay) / ($by - $ay) * ($bx - $ax);
                    }
                }
            }

            if ($crossings === []) {
                continue;
            }

            sort($crossings);
            $imageWidth = imagesx($image);

            for ($k = 0, $c = count($crossings); $k + 1 < $c; $k += 2) {
                $xStart = max(0, (int) ceil($crossings[$k] - 0.5));
                $xEnd = min($imageWidth - 1, (int) floor($crossings[$k + 1] - 0.5));

                for ($px = $xStart; $px <= $xEnd; $px++) {
                    imagesetpixel($image, $px, $row, $colorAt($px, $row));
                }
            }
        }
    }

    private function gradientRatio(
        GradientType $type,
        int $px,
        int $py,
        float $left,
        float $top,
        float $width,
        float $height
    ): float {
        switch ($type) {
            case GradientType::HORIZONTAL():
                $t = ($px - $left) / $width;
                break;
            case GradientType::DIAGONAL():
                $t = (($px - $left) / $width + ($py - $top) / $height) / 2;
                break;
            case GradientType::INVERSE_DIAGONAL():
                $t = ((($left + $width) - $px) / $width + ($py - $top) / $height) / 2;
                break;
            case GradientType::RADIAL():
                $cx = $left + $width / 2;
                $cy = $top + $height / 2;
                $maxDistance = sqrt(($width / 2) ** 2 + ($height / 2) ** 2);
                $t = $maxDistance < 1e-9 ? 0.0 : sqrt(($px - $cx) ** 2 + ($py - $cy) ** 2) / $maxDistance;
                break;
            case GradientType::VERTICAL():
            default:
                $t = ($py - $top) / $height;
                break;
        }

        return max(0.0, min(1.0, $t));
    }

    private function requireImage(): GdImage
    {
        if ($this->image === null) {
            throw new BadMethodCallException('No image has been started');
        }

        return $this->image;
    }

    private function colorIndex(GdImage $image, ColorInterface $color): int
    {
        $rgba = $this->rgba($color);

        return imagecolorallocatealpha($image, $rgba['r'], $rgba['g'], $rgba['b'], $rgba['a']);
    }

    /**
     * Normalises any Bacon color to an [r, g, b, a] map, where `a` is a GD alpha (0 opaque .. 127 transparent).
     *
     * @return array{r: int, g: int, b: int, a: int}
     */
    private function rgba(ColorInterface $color): array
    {
        $alpha = 100;

        if ($color instanceof Alpha) {
            $alpha = $color->getAlpha();
            $color = $color->getBaseColor();
        }

        $rgb = $color->toRgb();

        return [
            'r' => $rgb->getRed(),
            'g' => $rgb->getGreen(),
            'b' => $rgb->getBlue(),
            'a' => (int) round((100 - $alpha) / 100 * 127),
        ];
    }
}
