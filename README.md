Parses google drive spreadsheet and generates translation files
=======


## About

this tool parses google drive spreadsheet into translation files. 
You can use single spreadsheet to generate  translation files in various formats that can be used in frontend, backend and mobile development. 
The markup of the spreadsheet is described below, but nothing is as self explanatory as [an example](https://docs.google.com/spreadsheets/d/1AUAKxhuZyjYl4NdpQCLBcSZe2snKAOjcXArlHRIn_hM/edit?usp=sharing). 

## Configuration

Creating default configuration file and translations spreadsheet is easy as running tool and providing name for translations spreadsheet.

### persmissions

tool can work with existing spreadsheets, but then it needs the `DRIVE` scope - full access to your Drive. If you want to 
narrow down the permissions, then the tool need to create the spreadsheet to use `DRIVE_FILE` scope - meening tool will 
have access only to files created by it. 

In order to create spreadsheet run the tool in a volume without `translate.json` file - tool will ask you 
for `DRIVE_FILE` permissions, the spreadsheet will be created and the `translate.json` will be autogenerated.

### config file

In the `\lang` directory of your project put a json file named `translate.json`. 

```
{
  "fileId": "1AUAKxhuZyjYl4NdpQCLBcSZe2snKAOjcXArlHRIn_hM",
  "sheetName": "Sheet1",
  "accessType": "drive"
  "targets": [
    {
      "format": "json",
      "pattern": "translations.%locale%",
      "outputEmptyValues": false,
      "sections": [
        "CORE",
        "FRONT"
      ],
      "tags": [
        "mail",
        "error"
      ]
    }
  ]
}
```

**fileId** is unique id of spreadsheet with translations

**sheetName** is optional name of sheet from which translations should be read. Default value is `production`.
If sheet with given name won't be found the first sheet will be used.

**accessType** is the type of permission tool will ask you to grant. `drive` is the permission to all files in a drive, 
`file` is the permissions only to files created by the tool (see `permissions`). Property is optional and defaults 
to `drive`.

You can define multiple **targets** with the same or different format each. 
That way you can generate for example only frontend translations or divide translations into multiple domain files. 

**Format** key is obligatory and can be one from the list of supported formats : 

`['json', 'xlf', 'android', 'iOS']`.

**Pattern** key is optional and is used as a resulting filename pattern 
(without file extension that is added automatically). 
If ommited defaults to naming convention of a target:

- for `json` : `%locale%` - example: `en.json`, `pl.json`
- for `xlf` : `messages.%locale%` - example: `messages.en.xlf`, `messages.pl.xlf`
- for `android`: `values-%locale%/strings` - example: `values-en/strings.xml`, `values-pl/strings.xml`
- for `iOS` : `%locale%.lproj/Localizable` - example: `en.lproj/Localizable.strings`, `pl.lproj/Localizable.strings`

**outputEmptyValues** key is optional and define if translation keys with empty value should appear in output translation file.
Default is **false**

**Sections** key is optional and define array of sections (root key levels in the translation spreadsheet) 
that are to be included in the resulting files. You can provide a virtual value `["_all"]` to include everything
from the translations spreadsheet without mentioning every single section in it. 
 
**Tags** key is optional and define array of tags that are to be included in the resulting files. 
This is variation to sections if the structure is more complicated and you want to fine tune witch keys go to the
target on the single key basis instead of section basis. Tags are defined for every key in `###` column. Tags are separated with coma.
 
The result files will be placed in the same directory as the `translate.json` configuration file. 

### running the containter

`docker run -ti --rm -v -e CLIENT_ID=$CLIENT_ID -e PROJECT_ID=$PROJECT_ID -e CLIENT_SECRET=$CLIENT_SECRET <<lang directory>>:/lang babelsheet/babelsheet`

Where `$CLIENT_ID`, `$PROJECT_ID`, `$CLIENT_SECRET` are appropriate values of your Project created in Google APIs Developer Console

The parser will ask you to go to the authorization link and paste the access code. Then the access token will be 
saved in the `lang directory`.

### Google Spreadsheet markup:

Parser ignores every row until metadata row is found. 
Metadata row is first row that contains `>>>` marker. 
It defines first level of translation keys. Then multiple `>>>` follow to indicate next key levels. 
In that level you also define the various locales you want to put in the translations spreadsheet.  
Additionally you can introduce the tags column `###` where you can tag every key, and then fine tune the targets by
selecting tags that to be included. 

example : `###, >>>, >>>, >>>, en, fr, pl`

Every next row is parsed for translations (see example referenced at the beginning of this document). 
