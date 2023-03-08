# Git Diff eXtraction 
[![GitHub code size in bytes](https://img.shields.io/github/languages/code-size/vierilusen/git-diff-x)](https://github.com/vierilusen/git-diff-x)
[![GitHub issues](https://img.shields.io/github/issues/vierilusen/git-diff-x)](https://github.com/vierilusen/git-diff-x/issues)
[![Packagist PHP Version](https://img.shields.io/packagist/dependency-v/laravel-zero/laravel-zero/php)](https://github.com/vierilusen/git-diff-x/releases/tag/v1.0)

This project is made only for internal personal use for the needs of PT Bhinneka Life Indonesia. A CLI program for indetified diff beetwen repo branch to repo master in dev repo and generate diff for the needs deploy to DP_APP_DB repo automatically.

## Require
- [PHP 8.1+](https://www.php.net/releases/)
- [Git 2.0+](https://mirrors.edge.kernel.org/pub/software/scm/git/)

## Installation
Install GDX on Windows
1. Download cli zip file from this [release link](https://github.com/vierilusen/git-diff-x/releases/)
2. Extract zip file to C:\gdx
3. Settings your enviroment variable path to use gdx globally


## Usage/Examples

See all available commands and options:

```bash
C:\gdx> gdx
```

Usage:

```bash
C:\gdx> gdx generate:repo_name --option
```

Example:

```bash
C:\gdx> gdx generate:bls_app --push
```
