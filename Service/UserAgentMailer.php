<?php namespace Hampel\KnownBots\Service;

use XF\Service\AbstractService;
use XF\Util\File;

class UserAgentMailer extends AbstractService
{
	protected $toEmail;
	protected $agents = [];

	public function setToEmail($email)
	{
		$this->toEmail = $email;
	}

	public function setUserAgents(array $agents)
	{
		$this->agents = $agents;
	}

	public function mailUserAgents()
	{
        $addresses = $this->getValidAddresses();
        if (!$addresses)
        {
            return 0;
        }

        $version = $this->app->finder('XF:AddOn')->whereId('Hampel/KnownBots')->fetchOne()->version_string;
        $attachment = $this->createBotFile();

        // XenForo's mailer takes one recipient per message, so each address gets its own
        $sent = 0;
        foreach ($addresses as $address)
        {
            $mail = $this->app->mailer()->newMail();
            $mail->setTo($address);
            $mail->setContent(
                \XF::phrase('hampel_knownbots_email_subject', compact('version'))->render('raw'),
                \XF::phrase('hampel_knownbots_see_attachment')->render('raw')
            );
            $mail->getEmailObject()->attachFromPath($attachment, null, "text/plain");

            if ($mail->send())
            {
                $sent++;
            }
        }

        return $sent;
	}

    /**
     * The address may be a list, separated by commas or semicolons. An entry that is not a valid
     * address is reported to the error log by name and skipped, rather than failing the whole send.
     */
    protected function getValidAddresses()
    {
        $validator = $this->app->validator(\XF\Validator\Email::class);

        $valid = [];
        foreach (preg_split('/[,;]/', (string) $this->toEmail) as $address)
        {
            $address = trim($address);
            if ($address === '')
            {
                continue;
            }

            if ($validator->isValid($address))
            {
                $valid[$address] = $address;
            }
            else
            {
                \XF::logError("Known Bots: skipped invalid email address '{$address}' when emailing user agents");
            }
        }

        return array_values($valid);
    }

    protected function createBotFile()
    {
        $botList = '';
        foreach ($this->agents as $bot)
        {
            $botList .= $bot . PHP_EOL;
        }

        $tmpFile = File::getNamedTempFile("knownbots-" . date("YmdHis") . ".txt");
        file_put_contents($tmpFile, $botList);

        return $tmpFile;
    }
}
