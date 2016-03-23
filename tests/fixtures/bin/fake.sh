#!/bin/bash

FAKE_BIN_DIR=$(dirname $0)
TEST_DIR=$FAKE_BIN_DIR/../../temp
MESSAGE_FILE=$TEST_DIR/commit-message.txt
cat "$2" > $MESSAGE_FILE
