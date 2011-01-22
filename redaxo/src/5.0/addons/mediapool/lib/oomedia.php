<?php


/**
 * Object Oriented Framework: Bildet ein Medium des Medienpools ab
 * @package redaxo4
 * @version svn:$Id$
 */

class rex_ooMedia
{
  // id
  private $_id = "";
  // parent (FOR FUTURE USE!)
  private $_parent_id = "";
  // categoryid
  private $_cat_id = "";

  // categoryname
  private $_cat_name = "";
  // rex_ooMediacategory
  private $_cat = "";

  // filename
  private $_name = "";
  // originalname
  private $_orgname = "";
  // filetype
  private $_type = "";
  // filesize
  private $_size = "";

  // filewidth
  private $_width = "";
  // fileheight
  private $_height = "";

  // filetitle
  private $_title = "";

  // updatedate
  private $_updatedate = "";
  // createdate
  private $_createdate = "";

  // updateuser
  private $_updateuser = "";
  // createuser
  private $_createuser = "";

  /**
   * @access protected
   */
  protected function __construct($id = null)
  {
    $this->getMediaById($id);
  }

  /**
   * @access public
   */
  static public function getMediaByName($filename)
  {
    return rex_ooMedia :: getMediaByFileName($filename);
  }

  /**
   * @access public
   *
   * @example rex_ooMedia::getMediaByExtension('css');
   * @example rex_ooMedia::getMediaByExtension('gif');
   */
  static public function getMediaByExtension($extension)
  {
    global $REX;

    $extlist_path = $REX['INCLUDE_PATH'].'/generated/files/'.$extension.'.mextlist';
    if (!file_exists($extlist_path))
		{
			require_once ($REX['INCLUDE_PATH'].'/core/functions/function_rex_generate.inc.php');
    	rex_generateMediaExtensionList($extension);
		}

    $media = array();

    if (file_exists($extlist_path))
    {
      require_once ($extlist_path);

      if (isset($REX['MEDIA']['EXTENSION'][$extension]) && is_array($REX['MEDIA']['EXTENSION'][$extension]))
      {
        foreach($REX['MEDIA']['EXTENSION'][$extension] as $filename)
          $media[] = rex_ooMedia :: getMediaByFileName($filename);
      }
    }

    return $media;
  }

  /**
   * @access public
   */
  static public function getMediaByFileName($name)
  {
    global $REX;

    if ($name == '')
      return null;

    $media_path = $REX['INCLUDE_PATH'].'/generated/files/'.$name.'.media';
    if (!file_exists($media_path))
		{
			require_once ($REX['INCLUDE_PATH'] .'/core/functions/function_rex_generate.inc.php');
    	rex_generateMedia($name);
		}

    if (file_exists($media_path))
    {
      require_once ($media_path);
      $aliasMap = array(
        'file_id' => 'id',
        're_file_id' => 'parent_id',
        'category_id' => 'cat_id',
        'filename' => 'name',
        'originalname' => 'orgname',
        'filetype' => 'type',
        'filesize' => 'size'
      );

      $media = new rex_ooMedia();
      foreach($REX['MEDIA']['FILENAME'][$name] as $key => $value)
      {
        if(in_array($key, array_keys($aliasMap)))
          $var_name = '_'. $aliasMap[$key];
        else
          $var_name = '_'. $key;

        $media->$var_name = $value;
      }
      $media->_cat = null;
      $media->_cat_name = null;

      return $media;
    }

    return NULL;
  }

  /**
   * @access public
   */
  public function getId()
  {
    return $this->_id;
  }

  /**
   * @access public
   */
  public function getCategory()
  {
    if ($this->_cat === null)
    {
      $this->_cat = rex_ooMediaCategory :: getCategoryById($this->getCategoryId());
    }
    return $this->_cat;
  }

  /**
   * @access public
   */
  public function getCategoryName()
  {
    if ($this->_cat_name === null)
    {
      $this->_cat_name = '';
      $category = $this->getCategory();
      if (is_object($category))
        $this->_cat_name = $category->getName();
    }
    return $this->_cat_name;
  }

  /**
   * @access public
   */
  public function getCategoryId()
  {
    return $this->_cat_id;
  }

  /**
   * @access public
   */
  public function getParentId()
  {
    return $this->_parent_id;
  }

  /**
   * @access public
   */
  public function hasParent()
  {
    return $this->getParentId() != 0;
  }

  /**
   * @access public
   * @deprecated 12.10.2007
   */
  function getDescription()
  {
    return $this->getValue('med_description');
  }

  /**
   * @access public
   * @deprecated 12.10.2007
   */
  function getCopyright()
  {
    return $this->getValue('med_copyright');
  }

  /**
   * @access public
   */
  public function getTitle()
  {
    return $this->_title;
  }

  /**
   * @access public
   */
  public function getFileName()
  {
    return $this->_name;
  }

  /**
   * @access public
   */
  public function getOrgFileName()
  {
    return $this->_orgname;
  }

  /**
   * @access public
   */
  public function getPath()
  {
    global $REX;
    return $REX['HTDOCS_PATH'].'files';
  }

  /**
   * @access public
   */
  public function getFullPath()
  {
    return $this->getPath().'/'.$this->getFileName();
  }

  /**
   * @access public
   */
  public function getWidth()
  {
    return $this->_width;
  }

  /**
   * @access public
   */
  public function getHeight()
  {
    return $this->_height;
  }

  /**
   * @access public
   */
  public function getType()
  {
    return $this->_type;
  }

  /**
   * @access public
   */
  public function getSize()
  {
    return $this->_size;
  }

  /**
   * @access public
   */
  public function getFormattedSize()
  {
    return self::_getFormattedSize($this->getSize());
  }

  /**
   * @access protected
   */
  static protected function _getFormattedSize($size)
  {

    // Setup some common file size measurements.
    $kb = 1024; // Kilobyte
    $mb = 1024 * $kb; // Megabyte
    $gb = 1024 * $mb; // Gigabyte
    $tb = 1024 * $gb; // Terabyte
    // Get the file size in bytes.

    // If it's less than a kb we just return the size, otherwise we keep going until
    // the size is in the appropriate measurement range.
    if ($size < $kb)
    {
      return $size." Bytes";
    }
    elseif ($size < $mb)
    {
      return round($size / $kb, 2)." KBytes";
    }
    elseif ($size < $gb)
    {
      return round($size / $mb, 2)." MBytes";
    }
    elseif ($size < $tb)
    {
      return round($size / $gb, 2)." GBytes";
    }
    else
    {
      return round($size / $tb, 2)." TBytes";
    }
  }

  /**
   * Formats a datestamp with the given format.
   *
   * If format is <code>null</code> the datestamp is returned.
   *
   * If format is <code>''</code> the datestamp is formated
   * with the default <code>dateformat</code> (lang-files).
   *
   * @access public
   * @static
   */
  static public function _getDate($date, $format = null)
  {
    if ($format !== null)
    {
      if ($format == '')
      {
        // TODO Im Frontend gibts kein I18N
        // global $REX;
        //$format = $REX['I18N']->msg('dateformat');
        $format = '%a %d. %B %Y';
      }
      return strftime($format, $date);
    }
    return $date;
  }

  /**
   * @access public
   */
  public function getUpdateUser()
  {
    return $this->_updateuser;
  }

  /**
   * @access public
    * @see #_getDate
   */
  public function getUpdateDate($format = null)
  {
    return $this->_getDate($this->_updatedate, $format);
  }

  /**
   * @access public
   */
  public function getCreateUser()
  {
    return $this->_createuser;
  }

  /**
   * @access public
    * @see #_getDate
   */
  public function getCreateDate($format = null)
  {
    return $this->_getDate($this->_createdate, $format);
  }

  /**
   * @access public
   */
  public function toImage(array $params = array ())
  {
    global $REX;

    if(!is_array($params))
    {
      $params = array();
    }

    $path = $REX['HTDOCS_PATH'];
    if (isset ($params['path']))
    {
      $path = $params['path'];
      unset ($params['path']);
    }

    // Ist das Media ein Bild?
    if (!$this->isImage())
    {
      $path = 'media/';
      $file = 'file_dummy.gif';

      // Verwenden einer statischen variable, damit getimagesize nur einmal aufgerufen
      // werden muss, da es sehr lange dauert
      static $dummyFileSize;

      if (empty ($dummyFileSize))
      {
        $dummyFileSize = getimagesize($path.$file);
      }
      $params['width'] = $dummyFileSize[0];
      $params['height'] = $dummyFileSize[1];
    }
    else
    {
      $resize = false;

      // ResizeModus festlegen
      if (isset ($params['resize']) && $params['resize'])
      {
        unset ($params['resize']);
        // Resize Addon installiert?
        if (rex_ooAddon::isAvailable('image_resize'))
        {
          $resize = true;
          if (isset ($params['width']))
          {
            $resizeMode = 'w';
            $resizeParam = $params['width'];
            unset ($params['width']);
          }
          elseif (isset ($params['height']))
          {
            $resizeMode = 'h';
            $resizeParam = $params['height'];
            unset ($params['height']);
          }
          elseif (isset ($params['crop']))
          {
            $resizeMode = 'c';
            $resizeParam = $params['crop'];
            unset ($params['crop']);
          }
          else
          {
            $resizeMode = 'a';
            $resizeParam = 100;
          }

          // Evtl. Gr��eneinheiten entfernen
          $resizeParam = str_replace(array (
            'px',
            'pt',
            '%',
            'em'
          ), '', $resizeParam);
        }
      }

      // Bild resizen?
      if ($resize)
      {
        $file = 'index.php?rex_resize='.$resizeParam.$resizeMode.'__'.$this->getFileName();
      }
      else
      {
        // Bild 1:1 anzeigen
        $path .= 'files/';
        $file = $this->getFileName();
      }
    }

    $title = $this->getTitle();

    // Alternativtext hinzuf�gen
    if (!isset($params['alt']))
    {
      if ($title != '')
      {
        $params['alt'] = htmlspecialchars($title);
      }
    }

    // Titel hinzuf�gen
    if (!isset($params['title']))
    {
      if ($title != '')
      {
        $params['title'] = htmlspecialchars($title);
      }
    }

    // Evtl. Zusatzatrribute anf�gen
    $additional = '';
    foreach ($params as $name => $value)
    {
      $additional .= ' '.$name.'="'.$value.'"';
    }

    return sprintf('<img src="%s"%s />', $path.$file, $additional);
  }

  /**
   * @access public
   */
  public function toLink($attributes = '')
  {
    return sprintf('<a href="%s" title="%s"%s>%s</a>', $this->getFullPath(), $this->getDescription(), $attributes, $this->getFileName());
  }
  /**
   * @access public
   */
  public function toIcon(array $iconAttributes = array ())
  {
    global $REX;

    $ext = $this->getExtension();
    $icon = $this->getIcon();

    if(!isset($iconAttributes['alt']))
    {
      $iconAttributes['alt'] = '&quot;'. $ext .'&quot;-Symbol';
    }

    if(!isset($iconAttributes['title']))
    {
      $iconAttributes['title'] = $iconAttributes['alt'];
    }

    if(!isset($iconAttributes['style']))
    {
      $iconAttributes['style'] = 'width: 44px; height: 38px';
    }

    $attrs = '';
    foreach ($iconAttributes as $attrName => $attrValue)
    {
      $attrs .= ' '.$attrName.'="'.$attrValue.'"';
    }

    return '<img src="'.$icon.'"'.$attrs.' />';
  }

  /**
   * @access public
   * @static
   */
  static public function isValid($media)
  {
    return is_object($media) && is_a($media, 'rex_ooMedia');
  }

  /**
   * @access public
   */
  public function isImage()
  {
    return $this->_isImage($this->getFileName());
  }

  /**
   * @access public
   * @static
   */
  static public function _isImage($filename)
  {
    static $imageExtensions;

    if (!isset ($imageExtensions))
    {
      $imageExtensions = array (
        'gif',
        'jpeg',
        'jpg',
        'png',
        'bmp'
      );
    }

    return in_array(rex_ooMedia :: _getExtension($filename), $imageExtensions);
  }

  /**
   * @access public
   */
  public function isInUse()
  {
    global $REX;

    // Im Frontend gibts kein I18N
    if(!isset($REX['I18N']) || !is_object($REX['I18N']))
      $REX['I18N'] = rex_create_lang($REX['LANG']);

    $sql = rex_sql::factory();
    $filename = addslashes($this->getFileName());

    $values = array();
    for ($i = 1; $i < 21; $i++)
    {
      $values[] = 'value'.$i.' LIKE "%'.$filename.'%"';
    }

    $files = array();
    $filelists = array();
    for ($i = 1; $i < 11; $i++)
    {
      $files[] = 'file'.$i.'="'.$filename.'"';
      $filelists[] = '(filelist'.$i.' = "'.$filename.'" OR filelist'.$i.' LIKE "'.$filename.',%" OR filelist'.$i.' LIKE "%,'.$filename.',%" OR filelist'.$i.' LIKE "%,'.$filename.'" ) ';
    }

    $where = '';
    $where .= implode(' OR ', $files).' OR ';
    $where .= implode(' OR ', $filelists) .' OR ';
    $where .= implode(' OR ', $values);
    $query = 'SELECT DISTINCT article_id, clang FROM '.$REX['TABLE_PREFIX'].'article_slice WHERE '. $where;

    // deprecated since REX 4.3
    // ----- EXTENSION POINT
    $query = rex_register_extension_point('OOMEDIA_IS_IN_USE_QUERY', $query,
      array(
        'filename' => $this->getFileName(),
        'media' => $this,
      )
    );

    $warning = array();
    $res = $sql->getArray($query);
    if($sql->getRows() > 0)
    {
      $warning[0] = $REX['I18N']->msg('pool_file_in_use_articles').'<br /><ul>';
      foreach($res as $art_arr)
      {
        $aid = $art_arr['article_id'];
        $clang = $art_arr['clang'];
        $ooa = rex_ooArticle::getArticleById($aid, $clang);
        $name = $ooa->getName();
        $warning[0] .='<li><a href="javascript:openPage(\'index.php?page=content&amp;article_id='. $aid .'&amp;mode=edit&amp;clang='. $clang .'\')">'. $name .'</a></li>';
      }
      $warning[0] .= '</ul>';
    }

    // ----- EXTENSION POINT
    $warning = rex_register_extension_point('OOMEDIA_IS_IN_USE', $warning,
      array(
        'filename' => $this->getFileName(),
        'media' => $this,
      )
    );

    if (!empty($warning))
      return $warning;

    return false;
  }

  /**
   * @access public
   */
  public function toHTML($attributes = '')
  {
    global $REX;

    $file = $this->getFullPath();
    $filetype = $this->getExtension();

    switch ($filetype)
    {
      case 'jpg' :
      case 'jpeg' :
      case 'png' :
      case 'gif' :
      case 'bmp' :
        {
          return $this->toImage($attributes);
        }
      case 'js' :
        {
          return sprintf('<script type="text/javascript" src="%s"%s></script>', $file, $attributes);
        }
      case 'css' :
        {
          return sprintf('<link href="%s" rel="stylesheet" type="text/css"%s>', $file, $attributes);
        }
      default :
        {
          return 'No html-equivalent available for type "'.$filetype.'"';
        }
    }
  }

  /**
   * @access public
   */
  public function toString()
  {
    return 'rex_ooMedia, "'.$this->getId().'", "'.$this->getFileName().'"'."<br/>\n";
  }

  // new functions by vscope
  /**
    * @access public
   */
  public function getExtension()
  {
    return $this->_getExtension($this->_name);
  }

  /**
   * @access public
   * @static
   */
  static public function _getExtension($filename)
  {
    return substr(strrchr($filename, "."), 1);
  }

  /**
   * @access public
   */
  public function getIcon($useDefaultIcon = true)
  {
    global $REX;

    $ext = $this->getExtension();
    $folder = $REX['HTDOCS_PATH'] .'redaxo/media/';
    $icon = $folder .'mime-'.$ext.'.gif';

    // Dateityp für den kein Icon vorhanden ist
    if (!file_exists($icon))
    {
      if($useDefaultIcon)
        $icon = $folder.'mime-default.gif';
      else
        $icon = $folder.'mime-error.gif';
    }
    return $icon;
  }

  /**
   * @access public
   */
  static public function _getTableName()
  {
    global $REX;
    return $REX['TABLE_PREFIX'].'file';
  }

  /**
   * @access public
   * @return Returns <code>true</code> on success or <code>false</code> on error
   */
  public function save()
  {
    $sql = rex_sql::factory();
    $sql->setTable($this->_getTableName());
    $sql->setValue('re_file_id', $this->getParentId());
    $sql->setValue('category_id', $this->getCategoryId());
    $sql->setValue('filetype', $this->getType());
    $sql->setValue('filename', $this->getFileName());
    $sql->setValue('originalname', $this->getOrgFileName());
    $sql->setValue('filesize', $this->getSize());
    $sql->setValue('width', $this->getWidth());
    $sql->setValue('height', $this->getHeight());
    $sql->setValue('title', $this->getTitle());

    if ($this->getId() !== null)
    {
      $sql->addGlobalUpdateFields();
      $sql->setWhere('file_id='.$this->getId() . ' LIMIT 1');
      $success = $sql->update();
      if ($success)
        rex_deleteCacheMedia($this->getFileName());
      return $success;
    }
    else
    {
      $sql->addGlobalCreateFields();
      $success = $sql->insert();
      if ($success)
        rex_deleteCacheMediaList($this->getCategoryId());
      return $success;
    }
  }

  /**
   * @access public
   * @return Returns <code>true</code> on success or <code>false</code> on error
   */
  public function delete($filename = null)
  {
    global $REX;

    if($filename != null)
    {
      $OOMed = rex_ooMedia::getMediaByFileName($filename);
      if($OOMed)
      {
        return $OOMed->delete();
      }
    }else
    {
      $qry = 'DELETE FROM '.$this->_getTableName().' WHERE file_id = '.$this->getId().' LIMIT 1';
      $sql = rex_sql::factory();
      $sql->setQuery($qry);

      if($this->fileExists())
      {
        unlink($REX['MEDIAFOLDER'].DIRECTORY_SEPARATOR.$this->getFileName());
      }

      rex_deleteCacheMedia($this->getFileName());

      return $sql->getError();
    }
    return false;
  }

  public function fileExists($filename = null)
  {
    global $REX;

    if($filename === null)
    {
      $filename = $this->getFileName();
    }

    return file_exists($REX['MEDIAFOLDER'].DIRECTORY_SEPARATOR.$filename);
  }

  // allowed filetypes
  static public function getDocTypes()
  {
    static $docTypes = array (
      'bmp',
      'css',
      'doc',
      'docx',
      'eps',
      'gif',
      'gz',
      'jpg',
      'mov',
      'mp3',
      'ogg',
      'pdf',
      'png',
      'ppt',
      'pptx',
      'pps',
      'ppsx',
      'rar',
      'rtf',
      'swf',
      'tar',
      'tif',
      'txt',
      'wma',
      'xls',
      'xlsx',
      'zip'
    );
    return $docTypes;
  }

  static public function isDocType($type)
  {
    return in_array($type, rex_ooMedia :: getDocTypes());
  }

  // allowed image upload types
  static public function getImageTypes()
  {
    static $imageTypes = array (
      'image/gif',
      'image/jpg',
      'image/jpeg',
      'image/png',
      'image/x-png',
      'image/pjpeg',
      'image/bmp'
    );
    return $imageTypes;
  }

  static public function isImageType($type)
  {
    return in_array($type, rex_ooMedia :: getImageTypes());
  }

  static public function compareImageTypes($type1, $type2)
  {
    static $jpg = array (
      'image/jpg',
      'image/jpeg',
      'image/pjpeg'
    );

    return in_array($type1, $jpg) && in_array($type2, $jpg);
  }

  public function hasValue($value)
  {
    if (substr($value, 0, 1) != '_')
    {
      $value = "_".$value;
    }
    return isset($this->$value);
  }

  public function getValue($value)
  {
    if (substr($value, 0, 1) != '_')
    {
      $value = "_".$value;
    }

    // Extra-Abfrage, da die Variable _cat_name erst in getCategoryName() gesetzt wird
    if ($value == '_cat_name')
    {
      return $this->getCategoryName();
    }

    // damit alte rex_article felder wie copyright, description
    // noch funktionieren
    if($this->hasValue($value))
    {
      return $this->$value;
    }
    elseif ($this->hasValue('med'. $value))
    {
      return $this->getValue('med'. $value);
    }
  }

  /**
   * @access public
   * @deprecated 20.02.2010
   * Stattdessen getMediaByFileName() nutzen
   */
  static public function getMediaById($id)
  {
    global $REX;

    $id = (int) $id;
    if ($id==0)
      return null;

    $sql = rex_sql::factory();
    // $sql->debugsql = true;
    $sql->setQuery('SELECT filename FROM ' . self :: _getTableName() . ' WHERE file_id='.$id);
    if ($sql->getRows() == 1)
    {
      return self :: getMediaByFileName($sql->getValue('filename'));
    }

    return NULL;
  }
}