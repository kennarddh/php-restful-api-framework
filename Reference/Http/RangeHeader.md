# HTTP Range Header

## Reference

-   [Send File PHP](https://www.media-division.com/the-right-way-to-handle-file-downloads-in-php/)
-   [Obsolete RFC](https://datatracker.ietf.org/doc/html/draft-ietf-http-range-retrieval-00)
-   [Active RFC](https://www.rfc-editor.org/rfc/rfc9110.html#name-range-requests)
-   [MDN Reference](https://developer.mozilla.org/en-US/docs/Web/HTTP/Range_requests)

## Definition

### Boundary String

-   Random string to separate between multiple range content
-   Not specified by the RFC

## Conclusion

### Headers

#### Request: Range Header

-   `Range: {rangeUnit: bytes}={range: {rangeStart}-{rangeEnd}}`
-   Multiple range separated with comma

#### Response: Content Type Header for Multiple Ranges

-   Content-Type: multipart/byteranges; boundary={boundaryString}

#### Response: Declare Endpoint Can Receive Ranges

-   Accept-Ranges: bytes

### Response: Content-Range

-   Content-Range: bytes {startRange}-{endRange}/{contentLength}

### Status Code

-   Send 200 for entire content
-   Send 206 for partial content
-   Send 416 for out of range

### Sending Content

#### No Range Header or Every range is invalid

-   Send entire content

#### One Range is Present

-   Set `Content-Range` header
-   Send specified bytes

#### Multiple Range is Present

-   Send multiple content range
-   Separate every range with `--{boundaryString}`
-   Before content set `Content-Type` and `Content-Range` in body
-   ```text
    --{boundaryString}
    Content-Type: {contentTypeForXRange}
    Content-Range: bytes {startRange}-{endRange}/{fileLength}

    {content}
    --{boundaryString}
    Content-Type: {contentTypeForXRange}
    Content-Range: bytes {startRange}-{endRange}/{fileLength}

    {content}
    --{boundaryString}
    ```

### Parsing

-   If range start if bigger than end ignore range
-   Range start and end are inclusive
-   Range start from 0, 0 is first character in content
-   If range end with "{value}-" replace with "{value}-{fileLength - 1}"
-   If range start with "-{value}" replace with "{fileLength - value}-{fileLength - 1}"
