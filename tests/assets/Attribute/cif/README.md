## Folder Structure

Create a folder for every attribute type.

The folder name must match the handle of the attribute type.

In every folder we can have one or more test cases.

## Test Cases

### Imported/exported XML

The files for a test case have the same *base name* (for example: `test-1`).

The name of the XML file to be imported is built by adding `.xml` to the base name (for example: `test-1.xml`).

If the exported XML is different from the imported one, create an XML file by appending `.out.xml` to the base name (for example: `test-1.out.xml`).
If such file does not exist, we expect that the exported XML is the same as the imported one.

### Attributes Configuration

By default, attribute keys are created using the default settings for the attribute types.

To specify custom settings, create an XML file by adding `.options.xml` to the base name.

The root element of that file must be `<attributekey>` (no need to set attributes to it), and its child elements contain the attribute type-specific options.

For example, for creating `textarea` attribute key that contains rich text, you can create an `.options.xml` file like this:

```xml
<attributekey>
  <type mode="rich_text"/>
</attributekey>
```

### Test Case Configuration

In order to provide options to the test case, you can create a JSON file by adding `.json` to the base name.

The syntax of the JSON is described in the [`options-schema.json`](options-schema.json) file.
