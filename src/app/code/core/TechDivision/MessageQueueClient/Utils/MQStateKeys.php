<?php
	
/**
 * This class holds the priority keys used
 * as message state.
 *
 * @package mqserver
 * @subpackage utils
 * @author wagnert <tw@brainguide.com>
 * @version $Revision: 1.2 $ $Date: 2009-01-02 16:34:10 $
 * @copyright brainGuide AG
 * @link http://www.brainguide.com
 */
class MQStateKeys {
	
	/**
	 * Private constructor for marking 
	 * the class as utiltiy.
	 *
	 * @return void
	 */
	private final function __construct() { /* Class is a utility class */ }
	
	/**
	 * Returns the initialized MQStateKey for the
	 * passed priority key.
	 * 
	 * @param integer $key The state key to return the instance for
	 * @return MQStateKey The instance
	 */
	public static function get($key) {
		switch($key) { // check the passed key and return the requested MQStateKey instance
			case 1:
				return MQStateActive::get();
				break;
			case 2:
				return MQStatePaused::get();
				break;
			case 3:
				return MQStateToProcess::get();
				break;
			case 4:
				return MQStateInProgress::get();
				break;
			case 5:
				return MQStateProcessed::get();
				break;
			case 6:
				return MQStateFailed::get();
				break;
			case 7:
				return MQStateUnknown::get();
				break;
			default:
				throw new Exception("MQStateKey with key $key doesn't exist");
		}
	}
}

/**
 * This class holds the interface for all 
 * MQStateKeys used as message state.
 *
 * @package mqserver
 * @subpackage utils
 * @author wagnert <tw@brainguide.com>
 * @version $Revision: 1.2 $ $Date: 2009-01-02 16:34:10 $
 * @copyright brainGuide AG
 * @link http://www.brainguide.com
 */
interface MQStateKey {
	
	/**
	 * Returns the key value of the
	 * StateKey instance.
	 * 
	 * @return integer The key value
	 */
	public function getState();
}

/**
 * This class holds the MQStateKey used
 * for messages with the active state.
 *
 * @package mqserver
 * @subpackage utils
 * @author wagnert <tw@brainguide.com>
 * @version $Revision: 1.2 $ $Date: 2009-01-02 16:34:10 $
 * @copyright brainGuide AG
 * @link http://www.brainguide.com
 */
class MQStateActive implements MQStateKey {
	
	/**
	 * Holds the key for messages with an active state.
	 * @var integer
	 */
	const KEY = 1;
	
	/**
	 * The string value for the 'active' MQStateKey.
	 * @var string
	 */
	private $state = "active";
	
	/**
	 * Private constructor for marking 
	 * the class as utiltiy.
	 *
	 * @return void
	 */
	private final function __construct() { /* Class is a utility class */ }
	
	/**
	 * Returns a new instance of the MQStateKey.
	 * 
	 * @return MQStateActive The instance
	 */
	public static function get() {
		return new MQStateActive();
	}
	
	/**
	 * (non-PHPdoc)
	 * @see MQStateKey#getState()
	 */
	public function getState() {
		return MQStateActive::KEY;
	}
	
	/**
	 * Returns the string value for the active MQStateKey.
	 * 
	 * @return string The string value
	 */
	public function __toString() {
		return $this->state;
	}
}

/**
 * This class holds the MQStateKey used
 * for messages with the paused state.
 *
 * @package mqserver
 * @subpackage utils
 * @author wagnert <tw@brainguide.com>
 * @version $Revision: 1.2 $ $Date: 2009-01-02 16:34:10 $
 * @copyright brainGuide AG
 * @link http://www.brainguide.com
 */
class MQStatePaused implements MQStateKey {
	
	/**
	 * Holds the key for messages with the paused state.
	 * @var integer
	 */
	const KEY = 2;
	
	/**
	 * The string value for the 'paused' MQStateKey.
	 * @var string
	 */
	private $state = "paused";
	
	/**
	 * Private constructor for marking 
	 * the class as utiltiy.
	 *
	 * @return void
	 */
	private final function __construct() { /* Class is a utility class */ }
	
	/**
	 * Returns a new instance of the MQStateKey.
	 * 
	 * @return MQStatePaused The instance
	 */
	public static function get() {
		return new MQStatePaused();
	}
	
	/**
	 * (non-PHPdoc)
	 * @see MQStateKey#getState()
	 */
	public function getState() {
		return MQStatePaused::KEY;
	}
	
	/**
	 * Returns the string value for the paused MQStateKey.
	 * 
	 * @return string The string value
	 */
	public function __toString() {
		return $this->state;
	}
}

/**
 * This class holds the MQStateKey used
 * for messages to process.
 *
 * @package mqserver
 * @subpackage utils
 * @author wagnert <tw@brainguide.com>
 * @version $Revision: 1.2 $ $Date: 2009-01-02 16:34:10 $
 * @copyright brainGuide AG
 * @link http://www.brainguide.com
 */
class MQStateToProcess implements MQStateKey {
	
	/**
	 * Holds the key for messages with an in progress state.
	 * @var integer
	 */
	const KEY = 3;
	
	/**
	 * The string value for the 'toProcess' MQStateKey.
	 * @var string
	 */
	private $state = "toProcess";
	
	/**
	 * Private constructor for marking 
	 * the class as utiltiy.
	 *
	 * @return void
	 */
	private final function __construct() { /* Class is a utility class */ }
	
	/**
	 * Returns a new instance of the MQStateKey.
	 * 
	 * @return MQStateToProcess The instance
	 */
	public static function get() {
		return new MQStateToProcess();
	}
	
	/**
	 * (non-PHPdoc)
	 * @see MQStateKey#getState()
	 */
	public function getState() {
		return MQStateToProcess::KEY;
	}
	
	/**
	 * Returns the string value for the high MQStateKey.
	 * 
	 * @return string The string value
	 */
	public function __toString() {
		return $this->state;
	}
}

/**
 * This class holds the MQStateKey used
 * for messages in progress.
 *
 * @package mqserver
 * @subpackage utils
 * @author wagnert <tw@brainguide.com>
 * @version $Revision: 1.2 $ $Date: 2009-01-02 16:34:10 $
 * @copyright brainGuide AG
 * @link http://www.brainguide.com
 */
class MQStateInProgress implements MQStateKey {
	
	/**
	 * Holds the key for messages with an in progress state.
	 * @var integer
	 */
	const KEY = 4;
	
	/**
	 * The string value for the 'inProgress' MQStateKey.
	 * @var string
	 */
	private $state = "inProgress";
	
	/**
	 * Private constructor for marking 
	 * the class as utiltiy.
	 *
	 * @return void
	 */
	private final function __construct() { /* Class is a utility class */ }
	
	/**
	 * Returns a new instance of the MQStateKey.
	 * 
	 * @return MQStateInProgress The instance
	 */
	public static function get() {
		return new MQStateInProgress();
	}
	
	/**
	 * (non-PHPdoc)
	 * @see MQStateKey#getState()
	 */
	public function getState() {
		return MQStateInProgress::KEY;
	}
	
	/**
	 * Returns the string value for the high MQStateKey.
	 * 
	 * @return string The string value
	 */
	public function __toString() {
		return $this->state;
	}
}

/**
 * This class holds the MQStateKey used
 * for processed messages.
 *
 * @package mqserver
 * @subpackage utils
 * @author wagnert <tw@brainguide.com>
 * @version $Revision: 1.2 $ $Date: 2009-01-02 16:34:10 $
 * @copyright brainGuide AG
 * @link http://www.brainguide.com
 */
class MQStateProcessed implements MQStateKey {
	
	/**
	 * Holds the key for messages with an processed state.
	 * @var integer
	 */
	const KEY = 5;
	
	/**
	 * The string value for the 'processed' MQStateKey.
	 * @var string
	 */
	private $state = "processed";
	
	/**
	 * Private constructor for marking 
	 * the class as utiltiy.
	 *
	 * @return void
	 */
	private final function __construct() { /* Class is a utility class */ }
	
	/**
	 * Returns a new instance of the MQStateKey.
	 * 
	 * @return MQStateProcessed The instance
	 */
	public static function get() {
		return new MQStateProcessed();
	}
	
	/**
	 * (non-PHPdoc)
	 * @see MQStateKey#getState()
	 */
	public function getState() {
		return MQStateProcessed::KEY;
	}
	
	/**
	 * Returns the string value for the high MQStateKey.
	 * 
	 * @return string The string value
	 */
	public function __toString() {
		return $this->state;
	}
}

/**
 * This class holds the MQStateKey used
 * for processed messages.
 *
 * @package mqserver
 * @subpackage utils
 * @author wagnert <tw@brainguide.com>
 * @version $Revision: 1.2 $ $Date: 2009-01-02 16:34:10 $
 * @copyright brainGuide AG
 * @link http://www.brainguide.com
 */
class MQStateFailed implements MQStateKey {
	
	/**
	 * Holds the state key for failed messages.
	 * @var integer
	 */
	const KEY = 6;
	
	/**
	 * The string value for the 'failed' MQStateKey.
	 * @var string
	 */
	private $state = "failed";
	
	/**
	 * Private constructor for marking 
	 * the class as utiltiy.
	 *
	 * @return void
	 */
	private final function __construct() { /* Class is a utility class */ }
	
	/**
	 * Returns a new instance of the MQStateKey.
	 * 
	 * @return MQStateFailed The instance
	 */
	public static function get() {
		return new MQStateFailed();
	}
	
	/**
	 * (non-PHPdoc)
	 * @see MQStateKey#getState()
	 */
	public function getState() {
		return MQStateFailed::KEY;
	}
	
	/**
	 * Returns the string value for the high MQStateKey.
	 * 
	 * @return string The string value
	 */
	public function __toString() {
		return $this->state;
	}
}

/**
 * This class holds the MQStateKey used for
 * messages with unknown state.
 * 
 * Messages are turned to this state when they 
 * are running longer than ten minutes.
 *
 * @package mqserver
 * @subpackage utils
 * @author wagnert <tw@brainguide.com>
 * @version $Revision: 1.2 $ $Date: 2009-01-02 16:34:10 $
 * @copyright brainGuide AG
 * @link http://www.brainguide.com
 */
class MQStateUnknown implements MQStateKey {
	
	/**
	 * Holds the state key for failed messages.
	 * @var integer
	 */
	const KEY = 7;
	
	/**
	 * The string value for the 'unknown' MQStateKey.
	 * @var string
	 */
	private $state = "unknown";
	
	/**
	 * Private constructor for marking 
	 * the class as utiltiy.
	 *
	 * @return void
	 */
	private final function __construct() { /* Class is a utility class */ }
	
	/**
	 * Returns a new instance of the MQStateKey.
	 * 
	 * @return MQStateUnknown The instance
	 */
	public static function get() {
		return new MQStateUnknown();
	}
	
	/**
	 * (non-PHPdoc)
	 * @see MQStateKey#getState()
	 */
	public function getState() {
		return MQStateUnknown::KEY;
	}
	
	/**
	 * Returns the string value for the MQStateKey.
	 * 
	 * @return string The string value
	 */
	public function __toString() {
		return $this->state;
	}
}