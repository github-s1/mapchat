<?php
/**
 * GImport class file.
 *
 * @package go
 * @author  Boris Serebrov
 */

/**
 * GImport class implements recursive import of directories with caching.
 *
 * Import is performed recursively for specified path alias.
 * Classes found are cached, so the import process can be slow only first time.
 * Default cache path is 'runtime/classes'.
 *
 * Usage example:
 * <pre>
 *    $importer = new GImport;
 *    $importer->add('modules.myModule.*');
 * </pre>
 * This code will import all clasees from myModule module.
 *
 * GImport can also be configured as application component.
 * Add following to application components config:
 * <pre>
 * return array(
 *   ...
 *   'preload' => array('log', 'import'),
 *   ...
 *   'components' => array(
 *     'import' => array(
 *         'class'=>'GImport',
 *         'import' => array(
 *              // add directories to import and
 *              // put 'import' component to preload to trigger import
 *              // on application initialization
 *              'application.extensions.*',
 *          ),
 *     ),
 * <pre>
 *
 * To import additional classes in runtime use following code:
 * <pre>
 *    Yii::app()->import->add('modules.myModule.*');
 * </pre>
 *
 * Class names and paths will be added to Yii::$classMap and available for autoload.
 * Import performed only once for each alias. Class cache is saved into file in the (by default)
 * runtime/classes directory.
 * During development clean 'runtime/classes' after changing files structure or set '$cacheEnabled' property
 * to false to disable file cache.
 */
class GImport extends CApplicationComponent {

    /**
     * Array with initial imports.
     * @var array list of aliases to import
     */
    public $import = array();

    /**
     * @var array options for {@CFileHelper} when searching for class files.
     */
    public $scanOptions = array(
        'fileTypes'=>array('php'),
        'exclude'=>array(
            '.svn',
            '.git',
            '/assets',
            '/commands',
            '/transliteration',
            '/messages',
            '/templates',
            'views',
            'view.php',
        ),
    );

    /**
     * @var string path alias of the folder for cached import arrays
     */
    public $cacheDirAlias = 'application.runtime.classes';

    /**
     * Whether to enable caching.
     * @var boolean whether to enable caching, default - true.
     */
    public $cacheEnabled = true;

    /**
     * Initializes the application component.
     *
     * Overrides parent implementation to do initial import.
     */
    public function init() {
        parent::init();
        foreach ($this->import as $alias) {
            $this->add($alias);
        }
    }

    /**
     * Recursively imports all classes from the given alias.
     * @param string $alias yii path alias of the folder for recursive import
     */
    public function add($alias) {
        if (substr($alias, -1) !== '*') {
            //simple class alias, just import it
            return Yii::import($alias);
        }
        //do recursive import
        $alias = substr($alias, 0, -2);
        $cacheFilePath = $this->getCachePath($alias);
        $cache = $this->getCache($cacheFilePath);
        //get cached import array or scan files and write cache
        if (!$cache) {
            $cache = $this->buildCache($alias);
            if ($this->cacheEnabled) {
                $this->writeCache($cache, $cacheFilePath);
            }
        }
        Yii::$classMap = array_merge(Yii::$classMap, $cache);
    }

    /**
     * Performs recursive search for class files and builds cache array.
     * @param string $alias path alias of the folder with classes.
     * @return array classes cache
     */
    protected function buildCache($alias) {
        $basePath = Yii::getPathOfAlias($alias);
        $cache = array();
        $files = CFileHelper::findFiles($basePath, $this->scanOptions);
        foreach ($files as $file) {
            $pos = strpos($file, $basePath);
            if ($pos !== 0) {
                throw new CException("Invalid file '$file' found.");
            }
            $path = str_replace('\\', '/', substr($file, strlen($basePath)));

            $className = substr(basename($path), 0, -4);
            //class must have name with upper case first letter
            if (!preg_match('@^[a-zA-Z_](.*)$@s', $className, $matches)) {
                continue;
            }

            $path = str_replace('/', '.', dirname($path));

            if ($path === '.') {
                $path = '';
            }
            $cache[$className] = Yii::getPathOfAlias($alias . $path . '.' . $className) . '.php';
        }
        return $cache;
    }

    /**
     * Returns full path to cache file for specified alias of the folder to be imported.
     * @param string $alias path alias.
     * @return string cache file path.
     */
    protected function getCachePath($alias) {
        //create cache directory if necessary
        $filesCacheDir = Yii::getPathOfAlias($this->cacheDirAlias);
        if (!is_dir($filesCacheDir)) {
            if (!mkdir($filesCacheDir)) {
                throw new CException(Yii::t('go',
                    'GImport cache directory "{path}" can not be created. '.
                    'Please make sure the parent directory exists and is writable by the Web server process.',
                    array('{path}' => $filesCacheDir))
                );
            }
            @chmod($filesCacheDir, 0775);
        } else if (!is_writable($filesCacheDir)) {
            throw new CException(Yii::t('go',
                'GImport cache directory path "{path}" is invalid. '.
                'Please make sure the directory exists and is writable by the Web server process.',
                array('{path}' => $filesCacheDir))
            );
        }
        //get full path to the cache file
        return $filesCacheDir . DIRECTORY_SEPARATOR . $alias . '.php';
    }

    /**
     * Returns cache content by specified cache file path.
     * @param string $path alias.
     * @return array|null classes cache or null if there is no cache (or if cache is disabled).
     */
    protected function getCache($path) {
        if (is_file($path) && $this->cacheEnabled) {
            try {
                $cache = require $path;
            } catch (Exception $e) {
                return null;
            }
            if (is_array($cache)) {
                return $cache;
            }
        }
        return null;
    }

    /**
     * Writes cache to the file.
     * @param array $cache classes cache to write.
     * @param string $path of cache file.
     */
    protected function writeCache(array $cache, $path) {
        $config = new CConfiguration($cache);
        $cacheString = $config->saveAsString();
        if (file_exists($path) && !is_writable($path)) {
            throw new CException(Yii::t('go',
                'GImport cache file "{file}" is not writable. '.
                'Please make sure the file is writable by the Web server process.',
                array('{file}'=>$path))
            );
        } else {
            file_put_contents($path.'.tmp', "<?php return $cacheString;", LOCK_EX);
            @chmod($path.'.tmp', 0775);
            @rename($path.'.tmp', $path);
        }
    }
}
