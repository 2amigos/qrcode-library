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
     * @throws InvalidConfigException
     * @return string  the formatted photo. Makes sure is of the right image extension.
     */
    protected function getFormattedPhoto(): ?string
    {
        if ($this->photo !== null) {
            $ext = strtolower(substr(strrchr($this->photo, '.'), 1));

            if ($ext === 'jpeg' || $ext === 'jpg' || $ext === 'png' || $ext === 'gif') {
                $ext = strtoupper($ext);

                return 'PHOTO;VALUE=URL;TYPE=' . $ext . ':' . $this->photo;
            }

            throw new InvalidConfigException('Invalid format Image!');
        }

        return null;
    }
}
