<?php namespace Hampel\KnownBots\Service;

use XF\Service\AbstractService;

class BotMailer extends AbstractService
{
	protected $toEmail = '';
	protected $bots = [];

	public function setToEmail($email)
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
			$botList = '';
			foreach ($this->bots as $bot)
			{
				$botList .= "<li>{$bot}</li>" . PHP_EOL;
			}

			$mail = $this->getMail();
			$mail->setContent(
				\XF::phrase('hampel_knownbots_email_subject')->render('raw'),
				"<ul>" . PHP_EOL . $botList . PHP_EOL . "</ul>" . PHP_EOL
			);
			return $mail->queue();
		}
	}

	protected function getMail()
	{
		$mail = $this->app->mailer()->newMail();
		$mail->setTo($this->toEmail);

		return $mail;
	}
}
