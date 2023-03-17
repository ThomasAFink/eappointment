<?php
/**
 *
 * @package Zmsentities
 * @copyright BerlinOnline Stadtportal GmbH & Co. KG
 *
 */
namespace BO\Zmsentities\Helper;

use BO\Zmsentities\Client;
use \BO\Zmsentities\Process;
use \BO\Zmsentities\Collection\ProcessList;
use \BO\Zmsentities\Config;
use Twig\Loader\FilesystemLoader;
use Twig\Environment;
use Twig\Extensions\I18nExtension;
use Twig\Extensions\IntlExtension;

/**
 * @SuppressWarnings(Coupling)
 *
 */
class Messaging
{
    public static $icsRequiredForStatus = [
        'confirmed',
        'appointment',
        'deleted'
    ];

    public static $allowEmptyProcesses = [
        'overview'
    ];

    public static function isIcsRequired(
        \BO\Zmsentities\Config $config,
        \BO\Zmsentities\Process $process,
        $status
    ) {
        $client = $process->getFirstClient();
        $noAttachmentDomains = $config->toProperty()->notifications->noAttachmentDomains->get();
        $noAttachmentDomains = explode(',', (string)$noAttachmentDomains);
        foreach ($noAttachmentDomains as $matching) {
            if (trim($matching) && strpos($client->email, '@'.trim($matching))) {
                return false;
            }
        }
        return (in_array($status, self::$icsRequiredForStatus));
    }

    public static function isEmptyProcessListAllowed($status)
    {
        return (in_array($status, self::$allowEmptyProcesses));
    }

    protected static $templates = array(
        'notification' => array(
            'appointment' => 'notification_appointment.twig',
            'confirmed' => 'notification_confirmation.twig',
            'queued' => 'notification_confirmation.twig',
            'called' => 'notification_headsup.twig',
            'reminder' => 'notification_reminder.twig',
            'pickup' => 'notification_pickup.twig',
            'deleted' => 'notification_deleted.twig'
        ),
        'mail' => array(
            'queued' => 'mail_queued.twig',
            'appointment' => 'mail_confirmation.twig',
            'reminder' => 'mail_reminder.twig',
            'pickup' => 'mail_pickup.twig',
            'deleted' => 'mail_delete.twig',
            'blocked' => 'mail_delete.twig',
            'survey' => 'mail_survey.twig',
            'overview' => 'mail_processlist_overview.twig',
            'preconfirmed' => 'mail_preconfirmed.twig'
        ),
        'ics' => array(
            'appointment' => 'icsappointment.twig',
            'deleted' => 'icsappointment_delete.twig'
        ),
        'admin' => array(
            'deleted' => 'mail_admin_delete.twig',
            'blocked' => 'mail_admin_delete.twig',
            'updated' => 'mail_admin_update.twig'
        )
    );

    protected static function twigView(): Environment
    {
        $templatePath = TemplateFinder::getTemplatePath();
        $loader = new FilesystemLoader($templatePath);
        $loader->addPath($templatePath, 'zmsentities');
        $twig = new Environment($loader, array(
            //'cache' => '/cache/',
        ));
        $twig->addExtension(new I18nExtension());
        $twig->addExtension(new IntlExtension());
        return $twig;
    }

    public static function getMailContent(
        $processList,
        Config $config,
        $initiator = null,
        $status = 'appointment'
    ) {
        $collection = (new ProcessList)->testProcessListLength($processList, self::isEmptyProcessListAllowed($status));
        $mainProcess = $collection->getFirst();
        $date = (new \DateTimeImmutable())->setTimestamp(0);
        $client = (new Client());
        if ($mainProcess) {
            $collection = $collection->withoutProcessByStatus($mainProcess, $status);
            $date = $mainProcess->getFirstAppointment()->toDateTime()->format('U');
            $client = $mainProcess->getFirstClient();
        }

        $template = self::getTemplate('mail', $status);
        if ($initiator) {
            $template = self::getTemplate('admin', $status);
        }
        if (!$template) {
            $exception = new \BO\Zmsentities\Exception\TemplateNotFound("Template for status $status not found");
            $exception->data = $status;
            throw $exception;
        }
        $parameters = [
            'date' => $date,
            'client' => $client,
            'process' => $mainProcess,
            'processList' => $collection->sortByAppointmentDate(),
            'config' => $config,
            'initiator' => $initiator,
            // I copied the following line from the repo muenchner-customizations
            // I will now remove the file Messaging.php from muenchner-customizations in order to prevent future confusions like we had yesterday
            'appointmentLink' => base64_encode(json_encode(['id' => $mainProcess->id, 'authKey' => $mainProcess->authKey]))
        ];

        $message = self::twigView()->render('messaging/' . $template, $parameters);

        return $message;
    }

    public static function getScopeAdminProcessListContent(
        \BO\Zmsentities\Collection\ProcessList $processList,
        \BO\Zmsentities\Scope $scope,
        \DateTimeInterface $dateTime
    ) {
        $message = self::twigView()->render(
            'messaging/mail_scopeadmin_processlist.twig',
            array(
                'dateTime' => $dateTime,
                'processList' => $processList,
                'scope' => $scope
            )
        );
        return $message;
    }

    public static function getNotificationContent(Process $process, Config $config, $status = 'appointment')
    {
        $appointment = $process->getFirstAppointment();
        $template = self::getTemplate('notification', $status);
        if (!$template) {
            $exception = new \BO\Zmsentities\Exception\TemplateNotFound("Template for $process not found");
            $exception->data = $process;
            throw $exception;
        }
        $message = self::twigView()->render(
            'messaging/' . $template,
            array(
                'date' => $appointment->toDateTime()->format('U'),
                'client' => $process->getFirstClient(),
                'process' => $process,
                'config' => $config
            )
        );
        return $message;
    }

    protected static function getTemplate($type, $status)
    {
        $template = null;
        if (Property::__keyExists($type, self::$templates)) {
            if (Property::__keyExists($status, self::$templates[$type])) {
                $template = self::$templates[$type][$status];
            }
        }
        return $template;
    }

    public static function getMailSubject(
        Process $process,
        Config $config,
        $initiator = null,
        $status = 'appointment'
    ) {
        $appointment = $process->getFirstAppointment();
        $template = 'subjects.twig';
        $subject = self::twigView()->render(
            'messaging/' . $template,
            array(
                'date' => $appointment->toDateTime()->format('U'),
                'client' => $process->getFirstClient(),
                'process' => $process,
                'config' => $config,
                'initiator' => $initiator,
                'status' => $status
            )
        );
        $subject = trim($subject);
        return $subject;
    }

    public static function getMailIcs(
        Process $process,
        Config $config,
        $status = 'appointment',
        $initiator = null,
        $now = false
    ) {
        $ics = new \BO\Zmsentities\Ics();
        $template = self::getTemplate('ics', $status);
        $message = self::getMailContent($process, $config, $initiator, $status);
        $plainContent = self::getPlainText($message, "\\n");
        $appointment = $process->getFirstAppointment();
        $currentYear = $appointment->getStartTime()->format('Y');
        $icsString = self::twigView()->render(
            'messaging/' . $template,
            array(
                'date' => $appointment->toDateTime()->format('U'),
                'startTime' => $appointment->getStartTime()->format('U'),
                'endTime' => $appointment->getEndTime()->format('U'),
                'startSummerTime' =>
                    \BO\Zmsentities\Helper\DateTime::getSummerTimeStartDateTime($currentYear)->format('U'),
                'endSummerTime' =>
                    \BO\Zmsentities\Helper\DateTime::getSummerTimeEndDateTime($currentYear)->format('U'),
                'process' => $process,
                'timestamp' => (!$now) ? time() : $now,
                'message' => $plainContent
            )
        );
        $icsString = html_entity_decode($icsString);
        $ics->content = self::getTextWithFoldedLines($icsString);
        return $ics;
    }

    public static function getPlainText($content, $lineBreak = "\n")
    {
        $converter = new \League\HTMLToMarkdown\HtmlConverter();
        $converter->getConfig()->setOption('remove_nodes', 'script');
        $converter->getConfig()->setOption('strip_tags', true);
        $converter->getConfig()->setOption('hard_break', true);
        $converter->getConfig()->setOption('use_autolinks', false);
        $text = $converter->convert($content);
        $text = str_replace(',', '\,', $text);
        $text = str_replace(';', '\;', $text);
        $text = str_replace("\n", $lineBreak, $text);
        return trim($text);
    }

    public static function getTextWithFoldedLines($content)
    {
        $newLines = [];
        $lines = explode("\n", $content);
        foreach ($lines as $text) {
            $subline = '';
            while (strlen($text) > 75) {
                $line = mb_substr($text, 0, 72);
                $llength = mb_strlen($line);
                $subline .= $line.chr(13).chr(10).chr(32);
                $text = mb_substr($text, $llength);
            }
            if (!empty($text) && 0 < strlen($subline)) {
                $subline .= $text;
            }
            if (0 < strlen($subline)) {
                $newLines[] = $subline;
            }
            if (!empty($text) && '' == $subline) {
                $newLines[] = $text;
            }
        }
        return implode(chr(13).chr(10), $newLines);
    }
}
