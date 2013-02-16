<?php

/**
 * This class holds the priority keys used
 * as message priority.
 *
 * @package common
 * @subpackage utils
 * @author wagnert <tw@brainguide.com>
 * @version $Revision: 1.1 $ $Date: 2008-12-30 17:47:25 $
 * @copyright brainGuide AG
 * @link http://www.brainguide.com
 */
class PriorityKeys {
	
	/**
	 * Private constructor for marking 
	 * the class as utiltiy.
	 *
	 * @return void
	 */
	private final function __construct() { /* Class is a utility class */ }
	
	/**
	 * Returns the initialized PriorityKey for the
	 * passed priority key.
	 * 
	 * @param integer $key The priority key to return the instance for
	 * @return PriorityKey The instance
	 */
	public static function get($key) {
		switch($key) { // check the passed key and return the requested PriorityKey instance
			case 1:
				return PriorityLow::get();
				break;
			case 2:
				return PriorityMedium::get();
				break;
			case 3:
				return PriorityHigh::get();
				break;
			default:
				throw new Exception("PriorityKey with key $key doesn't exist");
		}
	}
}

/**
 * This class holds the interface for all 
 * PriorityKeys used as message priority.
 *
 * @package common
 * @subpackage utils
 * @author wagnert <tw@brainguide.com>
 * @version $Revision: 1.1 $ $Date: 2008-12-30 17:47:25 $
 * @copyright brainGuide AG
 * @link http://www.brainguide.com
 */
interface PriorityKey {
	
	/**
	 * Returns the key value of the
	 * PriorityKey instance.
	 * 
	 * @return integer The key value
	 */
	public function getPriority();
}

/**
 * This class holds the PriorityKey used
 * for low priority messages.
 *
 * @package common
 * @subpackage utils
 * @author wagnert <tw@brainguide.com>
 * @version $Revision: 1.1 $ $Date: 2008-12-30 17:47:25 $
 * @copyright brainGuide AG
 * @link http://www.brainguide.com
 */
class PriorityLow implements PriorityKey {
	
	/**
	 * Holds the key for messages with a low priority.
	 * @var integer
	 */
	const KEY = 1;
	
	/**
	 * The string value for the low PriorityKey.
	 * @var string
	 */
	private $priority = "low";
	
	/**
	 * Private constructor for marking 
	 * the class as utiltiy.
	 *
	 * @return void
	 */
	private final function __construct() { /* Class is a utility class */ }
	
	/**
	 * Returns a new instance of the PriorityKey.
	 * 
	 * @return PriorityLow The instance
	 */
	public static function get() {
		return new PriorityLow();
	}
	
	/**
	 * (non-PHPdoc)
	 * @see PriorityKey#getPriority()
	 */
	public function getPriority() {
		return PriorityLow::KEY;
	}
	
	/**
	 * Returns the string value for the low PriorityKey.
	 * 
	 * @return string The string value
	 */
	public function __toString() {
		return $this->priority;
	}
}

/**
 * This class holds the PriorityKey used
 * for medium priority messages.
 *
 * @package common
 * @subpackage utils
 * @author wagnert <tw@brainguide.com>
 * @version $Revision: 1.1 $ $Date: 2008-12-30 17:47:25 $
 * @copyright brainGuide AG
 * @link http://www.brainguide.com
 */
class PriorityMedium implements PriorityKey {
	
	/**
	 * Holds the key for messages with a medium priority.
	 * @var integer
	 */
	const KEY = 2;
	
	/**
	 * The string value for the medium PriorityKey.
	 * @var string
	 */
	private $priority = "medium";
	
	/**
	 * Private constructor for marking 
	 * the class as utiltiy.
	 *
	 * @return void
	 */
	private final function __construct() { /* Class is a utility class */ }
	
	/**
	 * Returns a new instance of the PriorityKey.
	 * 
	 * @return PriorityMedium The instance
	 */
	public static function get() {
		return new PriorityMedium();
	}
	
	/**
	 * (non-PHPdoc)
	 * @see PriorityKey#getPriority()
	 */
	public function getPriority() {
		return PriorityMedium::KEY;
	}
	
	/**
	 * Returns the string value for the medium PriorityKey.
	 * 
	 * @return string The string value
	 */
	public function __toString() {
		return $this->priority;
	}
}

/**
 * This class holds the PriorityKey used
 * for high priority messages.
 *
 * @package common
 * @subpackage utils
 * @author wagnert <tw@brainguide.com>
 * @version $Revision: 1.1 $ $Date: 2008-12-30 17:47:25 $
 * @copyright brainGuide AG
 * @link http://www.brainguide.com
 */
class PriorityHigh implements PriorityKey {
	
	/**
	 * Holds the key for messages with a high priority.
	 * @var integer
	 */
	const KEY = 3;
	
	/**
	 * The string value for the high PriorityKey.
	 * @var string
	 */
	private $priority = "high";
	
	/**
	 * Private constructor for marking 
	 * the class as utiltiy.
	 *
	 * @return void
	 */
	private final function __construct() { /* Class is a utility class */ }
	
	/**
	 * Returns a new instance of the PriorityKey.
	 * 
	 * @return PriorityHigh The instance
	 */
	public static function get() {
		return new PriorityHigh();
	}
	
	/**
	 * (non-PHPdoc)
	 * @see PriorityKey#getPriority()
	 */
	public function getPriority() {
		return PriorityHigh::KEY;
	}
	
	/**
	 * Returns the string value for the high PriorityKey.
	 * 
	 * @return string The string value
	 */
	public function __toString() {
		return $this->priority;
	}
}