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
        $version = $this->app->finder('XF:AddOn')->whereId('Hampel/KnownBots')->fetchOne()->version_string;

        $mail = $this->app->mailer()->newMail();
        $mail->setTo($this->toEmail);
        $mail->setContent(
            \XF::phrase('hampel_knownbots_email_subject', compact('version'))->render('raw'),
            \XF::phrase('hampel_knownbots_see_attachment')->render('raw')
        );

        $attachment = $this->createBotFile();

        $mail->getMessageObject()->attach(\Swift_Attachment::fromPath($attachment, "text/plain"));
        return $mail->send();
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
