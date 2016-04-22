<?php
namespace BO\Zmsentities\Collection;

class MailList extends BaseController
{

    public function addMail($mail)
    {
        if ($mail instanceof \BO\Zmsentities\Mail) {
            $this[] = clone $mail;
        }

        return $this;
    }
}
