<?php

/*
 * This file is part of the 2amigos/qrcode-library project.
 *
 * (c) 2amigOS! <http://2am.tech/>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Da\QrCode\Format;

use Da\QrCode\Exception\InvalidConfigException;
use Da\QrCode\Traits\EmailTrait;
use Da\QrCode\Traits\UrlTrait;

/**
 * Class vCard creates a valid vCard 4.0 QrCode string
 *
 * @author Antonio Ramirez <hola@2amigos.us>
 * @link https://www.2amigos.us/
 * @package Da\QrCode\Format
 */
class VCardFormat extends AbstractFormat
{
    use EmailTrait;
    use UrlTrait;

    /**
     * @var string[] image extensions allowed when referencing a photo by URL.
     */
    private const SUPPORTED_PHOTO_EXTENSIONS = ['jpeg', 'jpg', 'png', 'gif'];

    /**
     * @var array<string, string> extension to MIME type fallback map for inline photos.
     */
    private const PHOTO_MIME_TYPES = [
        'jpeg' => 'image/jpeg',
        'jpg' => 'image/jpeg',
        'png' => 'image/png',
        'gif' => 'image/gif',
    ];

    /**
     * @var string the name
     */
    public $name;

    /**
     * @var string the full name
     */
    public $fullName;

    /**
     * @var string the address
     */
    public $address;

    /**
     * @var string the nickname
     */
    public $nickName;

    /**
     * @var string the work phone
     */
    public $workPhone;

    /**
     * @var string the home phone
     */
    public $homePhone;

    /**
     * @var string a date in the format YYYY-MM-DD or ISO 860
     */
    public $birthday;

    /**
     * @var string the gender
     */
    public $gender;

    /**
     * @var string the categories. A list of "tags" that can be used to describe the object represented by this vCard.
     *             e.g., developer,designer,climber,swimmer
     */
    public $categories;

    /**
     * @var string the instant messaging and presence protocol (instant messenger id)
     */
    public $impp;

    /**
     * @var string the photo
     */
    public $photo;

    /**
     * @var string the role e.g., Executive
     */
    public $role;

    /**
     * @var string the name and optionally the unit(s) of the organization
     *             associated with the vCard object. This property is based on the X.520 Organization Name
     *             attribute and the X.520 Organization Unit attribute.
     */
    public $organization;

    /**
     * @var string notes
     */
    public $note;

    /**
     * @var string language of the user
     */
    public $lang;

    /**
     * @throws InvalidConfigException
     * @return string
     */
    public function getText(): string
    {
        $data = [];
        $data[] = 'BEGIN:VCARD';
        $data[] = 'VERSION:4.0';
        $data[] = "N:{$this->name}";
        $data[] = "FN:{$this->fullName}";
        $data[] = "ADR:{$this->address}";
        $data[] = "NICKNAME:{$this->nickName}";
        $data[] = "EMAIL;TYPE=PREF,INTERNET:{$this->email}";
        $data[] = "TEL;TYPE=WORK:{$this->workPhone}";
        $data[] = "TEL;TYPE=HOME:{$this->homePhone}";
        $data[] = "BDAY:{$this->birthday}";
        $data[] = "GENDER:{$this->gender}";
        $data[] = "CATEGORIES:{$this->categories}";
        $data[] = "IMPP:{$this->impp}";
        $data[] = $this->getFormattedPhoto();
        $data[] = "ROLE:{$this->role}";
        $data[] = "URL:{$this->url}";
        $data[] = "ORG:{$this->organization}";
        $data[] = "NOTE:{$this->note}";
        $data[] = "LANG:{$this->lang}";
        $data[] = 'END:VCARD';

        return implode("\n", array_filter($data));
    }

    /**
     * Builds the vCard `PHOTO` line.
     *
     * The {@see $photo} property accepts, in order of precedence:
     *  - a ready `data:` URI (e.g. `data:image/png;base64,...`) — embedded inline as-is;
     *  - a path to a readable local image file — read and embedded inline as a Base64 data URI (#69);
     *  - a remote URL or path ending in a supported image extension — referenced by URL (back-compat).
     *
     * @throws InvalidConfigException if the photo can not be read or has an unsupported format.
     * @return string|null the formatted `PHOTO` line, or null when no photo is set.
     */
    protected function getFormattedPhoto(): ?string
    {
        if ($this->photo === null || $this->photo === '') {
            return null;
        }

        // Already a data URI: embed inline (vCard 4.0 supports inline data values).
        // Only image data URIs are allowed, to avoid embedding arbitrary (e.g. HTML/JS) payloads.
        if (str_starts_with($this->photo, 'data:')) {
            foreach (self::PHOTO_MIME_TYPES as $mime) {
                if (str_starts_with($this->photo, 'data:' . $mime)) {
                    return 'PHOTO:' . $this->photo;
                }
            }

            throw new InvalidConfigException('Invalid format Image!');
        }

        // Readable local file: inline it as a Base64 data URI.
        if (is_file($this->photo)) {
            $contents = @file_get_contents($this->photo);

            if ($contents === false) {
                throw new InvalidConfigException('Unable to read photo file: ' . $this->photo);
            }

            return 'PHOTO:data:' . $this->detectPhotoMimeType($this->photo, $contents) . ';base64,'
                . base64_encode($contents);
        }

        // Remote URL or plain path: reference by URL, keeping the historical behaviour.
        $ext = strtolower(pathinfo((string) parse_url($this->photo, PHP_URL_PATH), PATHINFO_EXTENSION));

        if (in_array($ext, self::SUPPORTED_PHOTO_EXTENSIONS, true)) {
            return 'PHOTO;VALUE=URL;TYPE=' . strtoupper($ext) . ':' . $this->photo;
        }

        throw new InvalidConfigException('Invalid format Image!');
    }

    /**
     * Resolves the MIME type for an inline photo, preferring the real image type over the extension.
     *
     * @param string $path     the photo path (used as an extension fallback).
     * @param string $contents the raw image contents.
     * @return string the resolved MIME type.
     */
    private function detectPhotoMimeType(string $path, string $contents): string
    {
        $info = @getimagesizefromstring($contents);

        if ($info !== false && isset($info['mime'])) {
            return $info['mime'];
        }

        $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));

        return self::PHOTO_MIME_TYPES[$ext] ?? 'application/octet-stream';
    }
}
