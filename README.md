# PackageFactory.CachedFileUploads
## Cached file uploads for Neos

!!! This is an experimental prototype to validate the feasibility. It should not be used in projects !!!

Uploaded files are stored in persistent caches and are not imported as resources. 
This makes it much easier to get rid of them again as they are deleted automatically once the caches expire
or can be flushed via `./flow flow:cache:flush` or `./flow cache:collectgarbage`.

## Cache Configuration

The Cache is configured via Caches.yaml. It may be necessary to adjust the `defaultLifetime` or the used `backend`
for the actual environment.

```
# Metadata of the files
#
PackageFactory_CachedFileUploads_File:
  frontend: Neos\Cache\Frontend\VariableFrontend
  backend: Neos\Cache\Backend\FileBackend
  persistent: true
  backendOptions:
    defaultLifetime: 86400
    
# File content
#
PackageFactory_CachedFileUploads_Content:
  frontend: Neos\Cache\Frontend\StringFrontend
  backend: Neos\Cache\Backend\FileBackend
  persistent: true
  backendOptions:
    defaultLifetime: 86400
```

## Usage in Forms

You can use the package in the fusion forms like this. The followimg is needed:

1. An input for property `file` rendered by `Vendor.Site:Form.Upload` until `Neos.Fusion.Form:Upload` is adjusted.
2. A schema `Form.Schema.forType('PackageFactory\CachedFileUploads\Domain\UploadedFile')` for the file property   
3. Optionally a validator `PackageFactory\CachedFileUploads\Validator\UploadedFileTypeValidator'` for the file property
4. A form action that uses the uploaded file in the form action via {data.file} 

```
prototype(Vendor.Site:Content.FileUploadForm)  < prototype(Neos.Fusion.Form:Component.Field) {

    renderer = Neos.Fusion.Form:Runtime.RuntimeForm {
        process {
            content = afx`
                <Neos.Fusion.Form:FieldContainer label="Message" field.name="file" attributes.class="form-group clearfix">
                    <PackageFactory.CachedFileUploads:FileUpload attributes.class="form-control" />
                </Neos.Fusion.Form:FieldContainer>
            `
            schema {
                file = ${Form.Schema.forType('PackageFactory\CachedFileUploads\Domain\CachedFileUpload').isRequired().validator('PackageFactory\CachedFileUploads\Validator\UploadedFileValidator', {allowedExtensions:['txt', 'jpg']})}
            }
        }

        action {
            message {
                type = 'Neos.Fusion.Form.Runtime:Message'
                options.message = "Thank you for your file."
            }

            email {
                type = 'Neos.Fusion.Form.Runtime:Email'
                options {
                    recipientAddress = "sender@example.com"
                    senderAddress = "redipuent@example.com"
                    subject ="hello world"
                    text = "hello world"
                    attachments.upload = ${data.file}
                }
            }
        }
    }
} 
```

NOTE:: Until this can be integrated into `Neos.Fusion.Form:Upload` the prototype `PackageFactory.CachedFileUploads:FileUpload` has to be used
