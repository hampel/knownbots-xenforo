<?php namespace Tests\Unit;

use Hampel\KnownBots\Listener;
use Hampel\KnownBots\Option\SendUserAgents;
use Tests\TestCase;

/**
 * The add-on tells a reader of the forum's configuration which of its settings hold a credential.
 * The declaration is a list of names kept beside the options it describes, so it goes stale
 * silently when one is renamed - which is what these tests are for. None of them needs the
 * add-on that fires the event.
 */
class SecretsDeclarationTest extends TestCase
{
    /** Options or members that read like a credential and are not one, with the reason. */
    private const NOT_SECRETS = [];

    private const CREDENTIAL = '/key|token|secret|password|webhook/i';

    public function test_it_declares_this_add_on_only()
    {
        $this->assertSame(['Hampel/KnownBots'], array_keys($this->declarations()));
    }

    public function test_every_declared_secret_is_an_option_this_add_on_has()
    {
        $problems = [];
        foreach ($this->declared()['options'] AS $path)
        {
            $parts = explode('.', $path);
            $file = $this->addOnRoot() . "/_output/options/{$parts[0]}.json";
            if (!is_file($file))
            {
                $problems[] = "{$path}: no such option";
                continue;
            }

            $members = json_decode(file_get_contents($file), true)['sub_options'] ?? [];
            if (isset($parts[1]) && !in_array($parts[1], $members, true) && !in_array('*', $members, true))
            {
                $problems[] = "{$path}: not a member of {$parts[0]}";
            }
        }

        $this->assertSame([], $problems);
    }

    public function test_an_option_or_member_that_reads_like_a_credential_is_declared_or_decided()
    {
        $decided = array_merge(self::NOT_SECRETS, $this->declared()['options']);

        $undecided = [];
        foreach ($this->optionIds() AS $optionId)
        {
            if (!in_array($optionId, $decided, true) && preg_match(self::CREDENTIAL, $optionId))
            {
                $undecided[] = $optionId;
            }

            $file = $this->addOnRoot() . "/_output/options/{$optionId}.json";
            foreach (json_decode(file_get_contents($file), true)['sub_options'] ?? [] AS $member)
            {
                $path = "{$optionId}.{$member}";
                if (preg_match(self::CREDENTIAL, $member) && !in_array($path, $decided, true))
                {
                    $undecided[] = $path;
                }
            }
        }

        $this->assertSame([], $undecided, 'declare each as a secret, or add it to NOT_SECRETS');
    }

    public function test_the_declared_config_keys_are_the_ones_the_add_on_reads()
    {
        // claiming a key is what gets its value shown, so both are listed even though neither
        // holds a credential; the add-on reads no other config key
        $this->assertSame(['knownBotsApi', 'knownBotsDomain'], array_keys($this->declared()['config']));
        $this->assertSame([[], []], array_values($this->declared()['config']));
    }

    public function test_the_declared_token_members_are_where_the_add_on_reads_them()
    {
        $this->setOption('knownbotsSendUserAgents', [
            'enabled' => true,
            'validation_token' => 'made-up-validation-token',
            'api_token' => 'made-up-api-token',
        ]);

        // the paths declared above are the ones these getters read, so a renamed member fails here
        $this->assertSame('made-up-validation-token', SendUserAgents::validationToken());
        $this->assertSame('made-up-api-token', SendUserAgents::apiToken());
    }

    // ------------------------------------------------------------------

    private function declarations() : array
    {
        $declarations = [];
        Listener::adminApiRedaction($declarations);

        return $declarations;
    }

    private function declared() : array
    {
        return $this->declarations()['Hampel/KnownBots'] + ['options' => [], 'config' => []];
    }

    private function optionIds() : array
    {
        $ids = [];
        foreach (glob($this->addOnRoot() . '/_output/options/*.json') ?: [] AS $file)
        {
            if (basename($file) != '_metadata.json')
            {
                $ids[] = basename($file, '.json');
            }
        }

        return $ids;
    }

    private function addOnRoot() : string
    {
        return dirname(__DIR__, 2);
    }
}
