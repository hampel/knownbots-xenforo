<?php namespace Hampel\KnownBots\Service;

use XF\Service\AbstractService;

class BotMailer extends AbstractService
{
	protected $toEmail = [];
	protected $bots = [];

	public function setToEmail(array $email)
	{
		$this->toEmail = $email;
	}

	public function setBots(array $bots)
	{
		$this->bots = $bots;
	}

	public function mailBots()
	{
		if (!empty($this->bots))
		{
			$version = $this->app->finder('XF:AddOn')->whereId('Hampel/KnownBots')->fetchOne()->version_string;

			$botList = '';
			foreach ($this->bots as $bot)
			{
				$botList .= "<li>{$bot}</li>" . PHP_EOL;
			}

			$mail = $this->getMail();
			$mail->setContent(
				\XF::phrase('hampel_knownbots_email_subject', compact('version'))->render('raw'),
				"<ul>" . PHP_EOL . $botList . PHP_EOL . "</ul>" . PHP_EOL
			);
			return $mail->queue();
		}
	}

	protected function getMail()
	{
		$mail = $this->app->mailer()->newMail();
        $count = 0;
        foreach ($this->toEmail as $email)
        {
            if ($count == 0)
            {
                $mail->setTo($email);
            }
            else
            {
                $mail->getMessageObject()->addBcc($email);
            }

            $count++;
        }

		return $mail;
	}
}
