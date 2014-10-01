###
#
# eZMailing extension
#
# @category  eZpublish
# @package   eZpublish.eZMailing
# @author    Novactive <ezmailing@novactive.com>
# @link      http://www.novactive.com
#
##

PHP := php
USER := www-data
EZCACHE := ./bin/php/ezcache.php
EZGENAUTOLOAD := ./bin/php/ezpgenerateautoloads.php -e -p
EZROOT := $(CURDIR)/../../
SU := su $(USER) -c


EXTENSION_NAME := eZMailing
VERSION := 1.0
BUILDNAME := $(EXTENSION_NAME)-$(VERSION).tar.gz
TAR_EXCLUDES := --exclude-vcs --exclude="Makefile" --exclude=".git" --exclude="tests" --exclude="TODO" --exclude="build" --exclude=".gitignore" --exclude=".DS_Store" --exclude="test.php"
SYNC_EXCLUDES := --exclude=".git" --exclude="build" --exclude=".gitignore" --exclude=".DS_Store" --exclude="tests"

# HELP

help:
	@echo "Usage:"
	@echo "\tclear-ini : Clear ini cache"
	@echo "\tclear-template : Clear template cache"
	@echo "\tclear-all : Clear all cache"
	@echo "\tgenerate-extension-autoload : regenere the autoload extension file"
	@echo "\tbuild : creer le tar dans ./build"
	@echo ""
	
	
# clear cache, template, etc...
	
clear-ini: clean-ini
clean-ini: 
	@$(SU) "cd $(EZROOT); $(PHP) $(EZCACHE) --clear-tag=ini"

clear-template: clean-template	
clean-template: 
	@$(SU) "cd $(EZROOT); $(PHP) $(EZCACHE) --clear-tag=template"
	
clear-all: clean-all	
clean-all: 
	@$(SU) "cd $(EZROOT); $(PHP) $(EZCACHE) --clear-all"

# Generate autoload

gen-ext-autoload: generate-extension-autoload
generate-extension-autoload:
	@$(SU) "cd $(EZROOT); $(PHP) $(EZGENAUTOLOAD)"
	

sync-test:
	echo "Not configured"

sync:
	echo "Not configured"

	
# Build Archive		
build:: buildtar	
buildtar:
	mkdir -p build
	tar cvzf build/$(BUILDNAME) . $(TAR_EXCLUDES)
	@echo $(BUILDNAME) est disponible dans le répertoire "build."

