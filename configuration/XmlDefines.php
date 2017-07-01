<?php
/**
 * @module XmlDefines
 *
 * @brief defines for configuration related xml documents
 */
namespace afm
{
	define('CONFIGURATION_SECTION', 'configuration');

	// table loading/processing
	define('TABLE_ELEMENT', "table");
	define('COLUMNS_ELEMENT', "columns");
	define('COLUMN_ELEMENT', "column");
	define('DATASET_ELEMENT', "dataset");
	define('DATA_ELEMENT', "data");
	define('DEPENDENCIES_ELEMENT', "dependencies");
	define('DEPENDENCY_ELEMENT', "dependency");
	
	// attributes for tables and columns
	define('NAME_ATTR', "name");
	define('VERSION_ATTR', "version");
	define('TYPE_ATTR', "type");
	define('TYPE_LENGTH_ATTR', "typeLength");
	define('TYPE_PRECISION_ATTR', "typePrecision");
	define('ALLOW_NULL_ATTR', "allowNull");
	define('IS_INDEX_ATTR', "isIndex");
	define('IS_PRIMARY_ATTR', "isPrimary");
	define('DEFAULT_ATTR', "default");
	define('VALUE_ATTR', "value");
}	
?>