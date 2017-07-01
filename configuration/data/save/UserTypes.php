<?php
/**
 * @module user_types
 *
 * @brief creates the user_types table for the system
 */
 
//---------------------------------------
//  id, name, active
//---------------------------------------
function createUserTypeTable($dbInstance)
{
	$userTypeTable = &$dbInstance->createTable('user_types');
	
	$idColumn = &$userTypeTable->createColumn('id', INTEGER_TYPE, null, null, false, false, true);
	$nameColumn = &$userTypeTable->createColumn('name', CHAR_TYPE, 128, null, false, false, false);
	$activeColumn = &$userTypeTable->createColumn('active', BOOLEAN_TYPE, null, null, false, false, false);
	$activeColumn->setDefaultValue('false');
	
	if ($userTypeTable->create() == true)
	{
		// populate the default ones
		$userType = ['name'=>'user', 'active'=>'true'];
		$userTypeTable->addRow($userType);
	
		// second one id=2 is admin
		// users are '1'
		$userType['name'] = 'admin';
		$userTypeTable->addRow($userType);

		$userType['name'] = 'support';
		$userTypeTable->addRow($userType);

		$userType['name'] = 'debug';
		$userTypeTable->addRow($userType);

		$userType['name'] = 'other';
		$userTypeTable->addRow($userType);
	}
}
?>
