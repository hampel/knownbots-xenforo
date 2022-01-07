<?php namespace Hampel\KnownBots\Cache;

use League\Flysystem\FilesystemInterface;
use XF\Util\File;

class CodeCache implements CacheInterface
{
    /** @var FilesystemInterface */
    protected $fs;

    public function __construct(FilesystemInterface $fs)
    {
        $this->fs = $fs;
    }

    protected function getRawFile($key)
    {
        return File::getCodeCachePath() . "/known_bots/{$key}.php";
    }

    protected function getAbstractedFile($key)
    {
        return "code-cache://known_bots/{$key}.php";
    }

    public function getValue($key)
    {
        $file = $this->getRawFile($key);
        if (!file_exists($file) || !is_readable($file))
        {
            return null;
        }

        $data = include($file); // include file rather than reading it so we get the benefit of PHP file caching

        if (empty($data) || count($data) == 0)
        {
            return null;
        }

        return $data;
    }

    public function keyExists($key)
    {
        return $this->fs->has($this->getAbstractedFile($key));
    }

    public function setValue($key, $value)
    {
        $output = "<?php\nreturn " . var_export($value, true) . ';';

        $this->fs->put($this->getAbstractedFile($key), $output);
    }

    public function deleteValue($key)
    {
        $this->fs->delete($this->getAbstractedFile($key));
    }
}
