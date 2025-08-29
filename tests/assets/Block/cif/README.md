## Folder Structure

Creae a folder for every block type.

The folder name must much the handle of the block type.

In every folder we can have one or more test cases.

## Test Cases

### Imported/exported XML

The files for a test case have the same *base name* (for example: `test-1`).

The name of the XML file to be imported is built by adding `.xml` to the base name (for example: `test-1.xml`).
The code will import that file to create a new block, then it will export that block and check if the exported XML is the same as the imported one.

### Test Case Configuration

In order to provide options to the test case, you can create a JSON file by adding `.json` to the base name.

The syntax of the JSON is described in the [`options-schema.json`](options-schema.json) file.
