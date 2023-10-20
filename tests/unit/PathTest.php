<?php

namespace unit;
class PathTest extends \Codeception\Test\Unit
{
    public function testRoundedPath()
    {
        $rounded = (new \Da\QrCode\QrCode('2am Technologies'))
            ->setPathStyle(\Da\QrCode\Contracts\PathStyleInterface::ROUNDED)
            ->writeString();
        file_put_contents(codecept_data_dir('path/rounded.png'), $rounded);
        $rounded2 = (new \Da\QrCode\QrCode('2am Technologies'))
            ->setPathStyle(\Da\QrCode\Contracts\PathStyleInterface::ROUNDED)
            ->setPathIntensity(0.7)
            ->writeString();
        file_put_contents(codecept_data_dir('path/rounded2.png'), $rounded2);
        $this->assertEquals(
            $this->normalizeString(file_get_contents(codecept_data_dir('path/rounded.png'))),
            $this->normalizeString($rounded)
        );

        $this->assertEquals(
            $this->normalizeString(file_get_contents(codecept_data_dir('path/rounded2.png'))),
            $this->normalizeString($rounded2)
        );
    }

    public function testDotsPath()
    {
        $dots = (new \Da\QrCode\QrCode('2am Technologies'))
            ->setPathStyle(\Da\QrCode\Contracts\PathStyleInterface::DOTS)
            ->writeString();
        file_put_contents(codecept_data_dir('path/dots.png'), $dots);

        $dots2 = (new \Da\QrCode\QrCode('2am Technologies'))
            ->setPathStyle(\Da\QrCode\Contracts\PathStyleInterface::DOTS)
            ->setPathIntensity(0.7)
            ->writeString();
        file_put_contents(codecept_data_dir('path/dots2.png'), $dots2);

        $this->assertEquals(
            $this->normalizeString(file_get_contents(codecept_data_dir('path/dots.png'))),
            $this->normalizeString($dots)
        );

        $this->assertEquals(
            $this->normalizeString(file_get_contents(codecept_data_dir('path/dots2.png'))),
            $this->normalizeString($dots2)
        );
    }

    protected function normalizeString($string)
    {
        return str_replace(
            "\r\n", "\n", str_replace(
                "&#13;", "", $string
            )
        );
    }
}