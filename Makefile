.PHONY: all

DIR := data

all: $(DIR)/dois.zip

$(DIR)/public_profiles.tar.gz:
	wget 'http://files.figshare.com/2369121/orcid_data_dump.tar' -O $(DIR)/public_profiles.tar.gz

$(DIR)/json: | $(DIR)/public_profiles.tar.gz
	(cd $(DIR) && tar -xzf public_profiles.tar.gz --wildcards "./json/*")

$(DIR)/json-files.txt: | $(DIR)/json
	find $(DIR)/json -name "*.json" > $(DIR)/json-files.txt

$(DIR)/dois.json: | $(DIR)/json-files.txt
	php extract-dois.php $(DIR)/json-files.txt

$(DIR)/dois.zip: | $(DIR)/dois.json
	zip $(DIR)/dois.zip $(DIR)/dois.json
