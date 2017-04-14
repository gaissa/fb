#!/usr/bin/env bash

########################################################################
## script: fb_install.sh
## description: a script to download WordPress
##              and the required plugin(s)
## date: 2017-04-14
## author: gaissa <https://github.com/gaissa>
########################################################################

##COMMENT
function download() {

echo
echo "Downloading WordPress . . ."
echo
curl -O https://wordpress.org/latest.tar.gz
echo
echo "Uncompressing . . ."
echo
tar xzvf latest.tar.gz
echo
echo "Setup files . . ."
touch ./wordpress/.htaccess
chmod 660 ./wordpress/.htaccess
#cp ./wordpress/wp-config-sample.php ./wordpress/wp-config.php
mkdir ./wordpress/wp-content/upgrade
echo
echo "Downloading plugins . . ."
echo
curl -O https://downloads.wordpress.org/plugin/woocommerce.latest-stable.zip
curl -O https://downloads.wordpress.org/plugin/woocommerce-easy-booking-system.latest-stable.zip
curl -O https://downloads.wordpress.org/plugin/capability-manager-enhanced.latest-stable.zip
curl -O https://downloads.wordpress.org/plugin/manageview-your-posts-only.latest-stable.zip
echo
echo "Uncompressing"
echo
unzip woocommerce.latest-stable.zip
unzip woocommerce-easy-booking-system.latest-stable.zip
unzip capability-manager-enhanced.latest-stable.zip
unzip manageview-your-posts-only.latest-stable.zip
cp -avr woocommerce ./wordpress/wp-content/plugins/woocommerce
cp -avr woocommerce-easy-booking-system ./wordpress/wp-content/plugins/woocommerce-easy-booking-system
cp -avr capability-manager-enhanced ./wordpress/wp-content/plugins/capability-manager-enhanced
cp -avr manageview-your-posts-only ./wordpress/wp-content/plugins/manageview-your-posts-only
rm -rf woocommerce
rm -rf woocommerce-easy-booking-system
rm -rf capability-manager-enhanced
rm -rf manageview-your-posts-only

}

# COMMENT
function main() {

file="./latest.tar.gz"
if [ -f "$file" ];
then
	echo
	echo -e "$file> Already found! Download again? <YES>"
	echo
	read -e -p ": " choice
	if [ "$choice" == "YES" ];
	then
		echo
		echo "Backup up and remove the existing installations . . ."
		echo
		current_time=$(date "+%Y.%m.%d-%H.%M.%S")
		echo "Current Time : $current_time"
		mkdir -p old
	        cp -avr wordpress ./old/wordpress.$current_time
		rm -rf wordpress
		download
	else
		echo
		echo "Passing download . . ."
		echo
		exit
	fi
else
	download
fi

}

main
