<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Rick
 * Date: 21.12.11
 * Time: 19:31
 * To change this template use File | Settings | File Templates.
 */

/**
 * @property array $updates[] Contents update files
 * @property string $cmdResult
 */
class CmsUpdate extends CComponent
{
    private $_appPath;
    private $_updateDir;
    private $_toVersion;
    private $_updateServer = 'login.dwishman.ru';
    private $_cmd_result   = '';

    public function __construct($version = null)
    {
        $this->_appPath   = Yii::getPathOfAlias('application');
        $this->_updateDir = $this->_appPath .DS. 'update';
        $this->checkUpdateDir();

        if ($version) {
            $this->_toVersion = $version;
        }

        if (Yii::app()->params['updateServer']) {
            $this->_updateServer = Yii::app()->params['updateServer'];
        }
    }

    public static function version()
    {
        $file = Yii::getPathOfAlias('webroot').DS.'version.txt';
        return trim(file_get_contents($file));
    }

    public function update()
    {
        if (!$this->_toVersion)
            return false;

        $updates = $this->getUpdates();

        if ($updates) {
            $this->_cmd_result = $this->runPhingUpdate();
            $this->removeTmpFiles();
            $this->dbUpdate();

            return true;
        }

        return false;
    }

    public function getCmdResult()
    {
        return $this->_cmd_result;
    }

    private function checkUpdateDir()
    {
        if (!is_dir($this->_updateDir))
            mkdir($this->_updateDir);
    }

    private function getUpdates()
    {
        $filesToDownload = array('update.zip', 'update.sql', 'build.xml');
        $files           = array();

        foreach($filesToDownload as $f) {
            $url = 'http://'. $this->_updateServer .'/updates/'. $this->_toVersion .'/'. $f;
            $headers = @get_headers($url);

            if (strpos($headers[0], '200')===false)
                continue;
            if (!is_file($this->_updateDir .DS. $f)) {
                if (!copy($url, $this->_updateDir .DS. $f))
                    throw new CException('Не удалось скопировать файл обновлений');
            }
            $files[] = $this->_updateDir .DS. $f;
        }
        return $files;
    }

    private function runPhingUpdate()
    {
        //$command = 'phing -f '. $this->_updateDir .'/build.xml';
        $output = shell_exec('phing -f '. $this->_updateDir .'/build.xml');
        return $output;
    }

    private function removeTmpFiles()
    {
        $files = scandir($this->_updateDir);
        foreach($files as $f) {
            if (is_file($this->_updateDir .DS. $f))
                unlink($this->_updateDir .DS. $f);
        }
    }

    private function dbUpdate()
    {
        $dbUpdater = new CmsDbUpdate();
        $dbUpdater->update(true);
    }
}
