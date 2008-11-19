<?php

passthru("svn export --force ../web/concrete/blocks/ ./source/blocks");
passthru("svn export --force ../web/concrete/controllers/ ./source/controllers");
passthru("svn export --force ../web/concrete/helpers/ ./source/helpers");
passthru("svn export --force ../web/concrete/jobs/ ./source/jobs");
passthru("svn export --force -N ../web/concrete/libraries/ ./source/libraries");
passthru("svn export --force ../web/concrete/models/ ./source/models");

passthru("rsync -ave ssh ./source/ c5man@67.227.135.145:/home/c5man/generate_api/source/");

passthru("curl http://67.227.135.145/generate_api/generate.php");