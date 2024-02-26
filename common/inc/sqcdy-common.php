<?php
// PHP code that should be distributed to all projects goes here.

// EWWW/EasyIO - short-circuit and bypass auto-generation of extra srcset sizes
// https://docs.ewww.io/article/48-customizing-exactdn
add_filter( 'exactdn_srcset_multipliers', '__return_false' );
