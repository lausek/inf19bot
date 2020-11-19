<?php

require_once __DIR__ . '/../../vendor/autoload.php';

// Check if our boss has sent us an email.

class MailCheck extends Check
{
    const MAX_MESSAGE_SIZE = 4096;

    private $filter = null;

    function __construct()
    {
        $this->filter = new MoodleFilter;
    }

    function run(Response $response, $update = null)
    {
        $creds = Util::load_secret_from_json(Util::path('/secret/mail.json'));

        if (isset($creds['mailbox']) && isset($creds['email']) && isset($creds['password']))
        {
            $asset_dir = Util::path('/asset');

            if (!file_exists($asset_dir))
            {
                mkdir($asset_dir);
            }

            try
            {
                $inbox = new PhpImap\Mailbox(
                    $creds['mailbox'],
                    $creds['email'],
                    $creds['password'],
                    $asset_dir
                );

                $unread_ids = $inbox->searchMailbox('UNSEEN');

                if (empty($unread_ids))
                {
                    return;
                }

                foreach ($unread_ids as $unread_id)
                {
                    $mail = $inbox->getMail($unread_id);
                    if ($this->filter->is_forward_allowed($mail))
                    {
                        $msg = $this->filter->format_msg($mail);
                        if ($this->forward($mail, $msg, $response))
                        {
                            //$inbox->deleteMail($unread_id);
                        }
                        else
                        {
                            //$inbox->markMailAsUnread($unread_id);
                        }
                    }
                    $inbox->markMailAsUnread($unread_id);
                }
            }
            catch (Exception $e)
            {
                throw new Exception('email login credentials seem to be wrong');
            }
        }
    }

    function forward($mail, $msg, Response $response): bool
    {
        $len = mb_strlen($msg);

        for ($i = 0; $i < $len; $i += self::MAX_MESSAGE_SIZE)
        {
            $chunk = mb_substr($msg, $i, self::MAX_MESSAGE_SIZE);

            $response->add_message($chunk /*, $markup */);
        }

        // FIXME: can this be moved out of loop?
        if ($mail->hasAttachments())
        {
            foreach ($mail->getAttachments() as $attachment)
            {
                $asset_path = basename($attachment->filePath);
                $asset_url = Util::get_asset_url($asset_path);
                $response->add_document($attachment->name, $asset_url);
            }
        }

        $static_msg = Language::get('CHK_MAIL_RECEIVED');
        if (null !== $static_msg)
        {
            $response->add_message($static_msg);
        }

        return true;
    }
}
