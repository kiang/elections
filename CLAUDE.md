# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

Taiwan Election Directory (選舉黃頁) - A civic tech project presenting information about elections and candidates in Taiwan. Data sourced from the Central Election Commission (CEC) with crowdsourced updates.

**Live site:** https://elections.olc.tw/

## Technology Stack

- **Framework:** CakePHP 2.5.x (embedded in `/cake2` directory)
- **Language:** PHP 5.3.0+
- **Database:** MySQL/MariaDB
- **Frontend:** Bootstrap 2.x, jQuery, jQuery UI
- **Image Processing:** Requires PHP Imagick extension

## Running Console Commands

Shell commands are run via the CakePHP console:

```bash
# From project root
./cake2/lib/Cake/Console/cake <command> -app /home/kiang/public_html/elections

# Available shells:
# area, candidate, cec, election, bulletin, export, import, json, keyword, polygon, report
```

## Architecture

### MVC Structure

```
Controller/          # Request handlers (AppController has Auth, ACL, Session)
Model/              # Data models (AppModel sets recursive=-1, uses Containable)
View/               # CTP templates, Layouts/, Elements/
Plugin/             # Api/ (REST endpoints), Permissible/ (ACL management)
Console/Command/    # CLI shells for data import/export
```

### Key Models & Behaviors

- **Election** - Uses Tree behavior for hierarchical election structure (parent_id)
- **Area** - Geographic districts with tree structure and polygon geometry
- **Candidate** - Profiles with image processing (auto-resize to 512x512)
- All models use Containable behavior for explicit eager loading

### Database Relationships

- Elections have hierarchical parent-child relationships
- Many-to-many: areas_elections, candidates_tags, bulletins_elections
- ACL tables: acos, aros, aros_acos for permission management

### Caching

Long-term cache keys used:
- `ElectionsView{id}` - Election view pages
- `CandidatesView{id}` - Candidate view pages
- `ElectionsS{keyword}` - Search results

### REST API (Plugin/Api/)

- `/api/candidates/s?term=name` - Search candidates
- `/api/candidates/view/{id}` - Candidate details
- `/api/elections/s?term=keyword` - Search elections
- `/api/areas/index` - List areas

## Initial Setup

Copy configuration templates before first run:
```bash
cp -R tmp_default/ tmp
cp .htaccess.default .htaccess
cp webroot/.htaccess.default webroot/.htaccess
cp webroot/index.php.default webroot/index.php
cp Config/core.php.default Config/core.php
cp Config/database.php.default Config/database.php
```

Database: Import `Config/schema/schema.sql` for empty DB, or decompress and import `Config/sql/db_*.sql.gz` for historical data.

## Key Directories

- `Config/schema/` - Database schema
- `Config/sql/` - Historical data dumps (2014-2024)
- `webroot/media/` - Uploaded candidate images (UUID-based naming)
- `Vendor/` - Third-party libs (geoPHP, php-diff)
