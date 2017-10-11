BabelSheet
=======


## About

BabelSheet is a tool for working with translations. It parses Google Spreadsheet into proper translation files which could be in various formats that can be used almost anywhere. No more dealing with complicated paid translation services or problems with multiple users working at the same time!

## Markup

For BabelSheet to know how to handle your translations, You need to stick with very simple spreadsheet markup.

Rules:
* it ignores every row until metadata row is found
* metadata row is first row that contains `>>>` marker; it defines first level of translation keys
* any further `>>>` marker indicates next key levels. 
* you define various locales in next columns, but in same row as markers
* optionaly, you can introduce the tags column `###` where you can tag every key (then fine tune the targets by
selecting tags that to be included)
* every next row is parsed for translations

Example:

| ###        | >>>  | >>>    | >>>  | en        | pl  |
|------------|------|--------|------|-----------|-----|
|            | CORE |        |      |           |     |
|            |      | LABELS |      |           |     |
| tag1, tag2 |      |        | YES  | Yes       | Tak |
| tag1       |      |        | NO   | No        | Nie |
|            |      | COMMON |      |           |     |
|            |      |        | SMTH | Something | Coś |

Nothing is more self-explanatory then [a working example](https://docs.google.com/spreadsheets/d/1AUAKxhuZyjYl4NdpQCLBcSZe2snKAOjcXArlHRIn_hM/edit?usp=sharing). 

## Configuration

BabelSheet can work with existing spreadsheets or create a new one automagically. But be aware, if you want to use existing spreadsheet you need to give full access to yours account drive. When you let the file to be created for You, it only has access to this specific file.

* New spreadsheet

1. Run babelsheet using `docker run -ti --rm -v $(pwd):/lang tshio/babelsheet`
1. Authenticate
1. Enter spreadsheet name
1. Edit translate.json; add some targets
1. You are basically done now, enjoy your translations on rerunning the babelsheet :-)

* Existing spreadsheet

1. Create project in [Google APIs Developer Console](https://console.developers.google.com/apis/credentials/wizard?api=drive.googleapis.com) with Drive access
1. Download `client_secret.json` file from _APIs Developer Console_ from created project
1. Setup `translate.json` manually
1. Run babelsheet using `docker run -ti --rm -e SECRET=$(cat client_secret.json) -v $(pwd):/lang tshio/babelsheet`
1. Enjoy your translations on rerunning the babelsheet :-)


### Configuration File

BabelSheet uses JSON file named `translate.json` from `\lang` directory with structure as below:

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
