rsync -e ssh -rv --delete --stats --progress . wowdesigns@118.139.161.101:~/greatdeals/ --exclude-from 'exclude.txt'
