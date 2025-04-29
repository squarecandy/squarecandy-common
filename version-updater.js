// version-updater.js
module.exports.readVersion = function( contents ) {
	// handle style.css or plugin.php
	const regex = /Version:.*/gim;
	const found = contents.match( regex );
	if ( found ) {
		return found[ 0 ].replace( 'Version: ', '' );
	}
	return null;
};

module.exports.writeVersion = function( contents, version ) {
	// handle style.css or plugin.php
	const regex = /Version:.*/gim;
	const found = contents.match( regex );
	if ( found ) {
		return contents.replace( found[ 0 ], 'Version: ' + version );
	}
	return contents;
};
