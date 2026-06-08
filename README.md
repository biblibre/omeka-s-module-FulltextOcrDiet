# FulltextDiet (Omeka S module)

[FulltextDiet](https://git.biblibre.com/omeka-s/FulltextDiet) is a
module for Omeka S. It allows administrators to exclude one or more properties
from Omeka S fulltext indexation, primarily designed to prevent large OCR text
values from bloating the fulltext search index.

## Goals

The primary goal of this module is to keep the fulltext search index lean and
relevant by excluding verbose or large properties (such as OCR-extracted text)
that would otherwise degrade search performance and cause database issues.

Excluded properties are configurable via the administration interface.

## Requirements

* Omeka S >= 4.0.0

## Quick start

1. [Add the module to Omeka S](https://omeka.org/s/docs/user-manual/modules/#adding-modules-to-omeka-s)
2. In the administration interface, go to **Modules > FulltextDiet > Configure**
3. Select one or more properties to exclude from fulltext indexation (e.g. `bibo:content`, `extracttext:extracted_text`)
4. Save — the exclusion takes effect immediately for all subsequent indexation

## Features

- Exclude one or more properties from Omeka S core fulltext indexation
- Configuration via the Omeka S administration interface using the native property selector
- Works at the Doctrine criteria level: excluded properties are never loaded
  during fulltext index building, avoiding memory and database issues
- Default exclusion: `extracttext:extracted_text`

## How to contribute

You can contribute to this module in many ways. Discover how by reading
[Contributing](CONTRIBUTING.md).

## Contributors / Sponsors

FulltextDiet was developed by BibLibre.

## License

FulltextDiet is distributed under the GNU General Public License, version 3 (GPLv3).
The full text of this license is given in the `LICENSE` file.
