<?php

passthru("svn export --force ../web/concrete/blocks/ ./source/blocks");
passthru("svn export --force ../web/concrete/controllers/ ./source/controllers");
passthru("svn export --force ../web/concrete/helpers/ ./source/helpers");
passthru("svn export --force ../web/concrete/jobs/ ./source/jobs");
passthru("svn export --force -N ../web/concrete/libraries/ ./source/libraries");
passthru("svn export --force ../web/concrete/models/ ./source/models");
passthru("svn export --force ../web/concrete/packages/ ./source/packages");
passthru("svn export --force ../web/concrete/single_pages/ ./source/single_pages");
passthru("svn export --force ../web/concrete/tutorials/ ./source/tutorials");

passthru("rsync -ave ssh ./source/ andrew@concrete5.org:/home/andrew/generate_api/source/");

passthru("curl http://www.concrete5.org/generate_api/generate.php");