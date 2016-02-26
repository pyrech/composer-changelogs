#!/bin/bash

git -C "$1" commit -F "$2" composer.lock
