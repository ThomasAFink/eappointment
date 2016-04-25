<?php
/**
 *
 * @package Zmsappointment
 * @copyright BerlinOnline Stadtportal GmbH & Co. KG
 *
 */
namespace BO\Zmsapi\Notification;

class Mail extends Base
{

    /*
     * TODO - Um das ICS Attachment zu holen gibt es den API aufruf der auch funktioniert.
     * Nun muss hier aber der Content abgefragt werden der für die entsprechende Mail vorgesehen wird.
     * Soll dieser ebenfalls aus der API / Notification Folder geholt werden?
     * Hier gibt es schon eine Funktion (createConfirmMessage()) die den Text erstellt. Aber es bedarf doch einer
     * neuen API Route oder? Beispiel (/process/id/authKey/mail/{confirm,reminder,info}
     *
     * */
    public static function getEntityData(\BO\Zmsentities\Process $process)
    {
        $content = self::createMessage($process);
        $entity = new \BO\Zmsentities\Mail();
        $entity->process['id'] = $process->id;
        $entity->subject = self::createSubject($process);
        $entity->createIP = $process->createIP;
        $entity->department['id'] = $process['scope']['department']['id'];
        $entity->multipart = [
            array(
                'mime' => 'text/html',
                'content' => $content,
                'base64' => true
            ),
            array(
                'mime' => 'text/plain',
                'content' => $entity->toPlainText($content),
                'base64' => true
            )
        ];
        return $entity;
    }
}
