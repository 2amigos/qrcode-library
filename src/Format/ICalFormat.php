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

/**
 * ICal creates a valid ICal format string
 *
 * @author Antonio Ramirez <hola@2amigos.us>
 * @link https://www.2amigos.us/
 * @package Da\QrCode\Format
 */
class ICalFormat extends AbstractFormat
{
    /**
     * @var string the event summary
     */
    public $summary;
    /**
     * @var integer the unix timestamp of the start date of the event
     */
    public $startTimestamp;
    /**
     * @var integer the unix timestamp of the end date of the event
     */
    public $endTimestamp;

    /**
     * @inheritdoc
     */
    public function getText(): string
    {
        $data = [];
        $data[] = 'BEGIN:VEVENT';
        $data[] = "SUMMARY:{$this->summary}";
        $data[] = "DTSTART:{$this->unixToICal($this->startTimestamp)}";
        $data[] = "DTEND:{$this->unixToICal($this->endTimestamp)}";
        $data[] = 'END:VEVENT';

        return implode("\n", $data);
    }

    /**
     * Converts a unix timestamp to ICal format. Timezones are assumed to be included into the timestamp.
     *
     * @param int $value the unix timestamp to convert
     *
     * @return bool|string the formatted date
     */
    protected function unixToICal($value)
    {
        return date("Ymd\THis\Z", $value);
    }
}
