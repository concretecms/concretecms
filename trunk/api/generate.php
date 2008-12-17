<?php

passthru("svn export --force ../../branches/releases/5.1.0/concrete/blocks/ ./source/blocks");
passthru("svn export --force ../../branches/releases/5.1.0/concrete/controllers/ ./source/controllers");
passthru("svn export --force ../../branches/releases/5.1.0/concrete/helpers/ ./source/helpers");
passthru("svn export --force ../../branches/releases/5.1.0/concrete/jobs/ ./source/jobs");
passthru("svn export --force -N ../../branches/releases/5.1.0/concrete/libraries/ ./source/libraries");
passthru("svn export --force ../../branches/releases/5.1.0/concrete/models/ ./source/models");

passthru("rsync -ave ssh ./source/ c5man@67.227.135.145:/home/c5man/generate_api/source/");

passthru("curl http://67.227.135.145/generate_api/generate.php");