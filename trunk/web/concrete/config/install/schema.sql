

CREATE TABLE AreaGroupBlockTypes (
cID                      INTEGER(10) UNSIGNED NOT NULL DEFAULT 0,
arHandle                 VARCHAR(255) NOT NULL,
gID                      INTEGER(10) UNSIGNED NOT NULL DEFAULT 0,
uID                      INTEGER(10) UNSIGNED NOT NULL DEFAULT 0,
btID                     INTEGER(10) UNSIGNED NOT NULL DEFAULT 0,
                 PRIMARY KEY (cID, arHandle, gID, uID, btID)
);

CREATE TABLE AreaGroups (
cID                      INTEGER(10) UNSIGNED NOT NULL DEFAULT 0,
arHandle                 VARCHAR(255) NOT NULL,
gID                      INTEGER(10) UNSIGNED NOT NULL DEFAULT 0,
uID                      INTEGER(10) UNSIGNED NOT NULL DEFAULT 0,
agPermissions            VARCHAR(64) NOT NULL,
                 PRIMARY KEY (cID, arHandle, gID, uID)
);

CREATE TABLE Areas (
arID                     INTEGER(10) UNSIGNED NOT NULL AUTO_INCREMENT,
cID                      INTEGER(10) UNSIGNED NOT NULL DEFAULT 0,
arHandle                 VARCHAR(255) NOT NULL,
arOverrideCollectionPermissions TINYINT(1) NOT NULL DEFAULT 0,
arInheritPermissionsFromAreaOnCID INTEGER(10) UNSIGNED NOT NULL DEFAULT 0,
                 PRIMARY KEY (arID)
);

CREATE TABLE AttributeSetKeys (
akID                     INTEGER(10) UNSIGNED NOT NULL DEFAULT 0,
asID                     INTEGER(10) UNSIGNED NOT NULL DEFAULT 0,
displayOrder             INTEGER(10) UNSIGNED NOT NULL DEFAULT 0,
                 PRIMARY KEY (akID, asID)
);

CREATE TABLE AttributeSets (
asID                     INTEGER(10) UNSIGNED NOT NULL AUTO_INCREMENT,
asName                   VARCHAR(255),
asHandle                 VARCHAR(255) NOT NULL,
akCategoryID             INTEGER(10) UNSIGNED NOT NULL DEFAULT 0,
pkgID                    INTEGER(10) UNSIGNED NOT NULL DEFAULT 0,
                 PRIMARY KEY (asID)
);

CREATE TABLE AttributeKeys (
akID                     INTEGER(10) UNSIGNED NOT NULL AUTO_INCREMENT,
akHandle                 VARCHAR(255) NOT NULL,
akName                   VARCHAR(255) NOT NULL,
akIsSearchable           TINYINT(1) NOT NULL DEFAULT 0,
akIsSearchableIndexed    TINYINT(1) NOT NULL DEFAULT 0,
akIsAutoCreated          TINYINT(1) NOT NULL DEFAULT 0,
akIsColumnHeader         TINYINT(1) NOT NULL DEFAULT 0,
akIsEditable             TINYINT(1) NOT NULL DEFAULT 0,
atID                     INTEGER(10) UNSIGNED,
akCategoryID             INTEGER(10) UNSIGNED,
pkgID                    INTEGER(10) UNSIGNED,
                 PRIMARY KEY (akID)
);

ALTER TABLE AttributeKeys ADD  UNIQUE INDEX akHandle  (akHandle, akCategoryID);

CREATE TABLE AttributeKeyCategories (
akCategoryID             INTEGER(10) UNSIGNED NOT NULL AUTO_INCREMENT,
akCategoryHandle         VARCHAR(255) NOT NULL,
akCategoryAllowSets      SMALLINT(4) NOT NULL DEFAULT 0,
pkgID                    INTEGER(10) UNSIGNED,
                 PRIMARY KEY (akCategoryID)
);

CREATE TABLE AttributeTypeCategories (
atID                     INTEGER(10) UNSIGNED NOT NULL DEFAULT 0,
akCategoryID             INTEGER(10) UNSIGNED NOT NULL DEFAULT 0,
                 PRIMARY KEY (atID, akCategoryID)
);

CREATE TABLE AttributeTypes (
atID                     INTEGER(10) UNSIGNED NOT NULL AUTO_INCREMENT,
atHandle                 VARCHAR(255) NOT NULL,
atName                   VARCHAR(255) NOT NULL,
pkgID                    INTEGER(10) UNSIGNED,
                 PRIMARY KEY (atID)
);

CREATE TABLE AttributeValues (
avID                     INTEGER(10) UNSIGNED NOT NULL AUTO_INCREMENT,
akID                     INTEGER(10) UNSIGNED,
avDateAdded              DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
uID                      INTEGER(10) UNSIGNED,
atID                     INTEGER(10) UNSIGNED,
                 PRIMARY KEY (avID)
);

CREATE TABLE BlockRelations (
brID                     INTEGER(10) UNSIGNED NOT NULL AUTO_INCREMENT,
bID                      INTEGER(10) UNSIGNED NOT NULL DEFAULT 0,
originalBID              INTEGER(10) UNSIGNED NOT NULL DEFAULT 0,
relationType             VARCHAR(50) NOT NULL,
                 PRIMARY KEY (brID)
);

CREATE TABLE BlockTypes (
btID                     INTEGER(10) UNSIGNED NOT NULL AUTO_INCREMENT,
btHandle                 VARCHAR(32) NOT NULL,
btName                   VARCHAR(128) NOT NULL,
btDescription            TEXT,
btActiveWhenAdded        TINYINT(1) NOT NULL DEFAULT 1,
btCopyWhenPropagate      TINYINT(1) NOT NULL DEFAULT 0,
btIncludeAll             TINYINT(1) NOT NULL DEFAULT 0,
btIsInternal             TINYINT(1) NOT NULL DEFAULT 0,
btInterfaceWidth         INTEGER(10) UNSIGNED NOT NULL DEFAULT 400,
btInterfaceHeight        INTEGER(10) UNSIGNED NOT NULL DEFAULT 400,
pkgID                    INTEGER(10) UNSIGNED NOT NULL DEFAULT 0,
                 PRIMARY KEY (btID)
);

ALTER TABLE BlockTypes ADD  UNIQUE INDEX btHandle  (btHandle);

CREATE TABLE Blocks (
bID                      INTEGER(10) UNSIGNED NOT NULL AUTO_INCREMENT,
bName                    VARCHAR(60),
bDateAdded               DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
bDateModified            DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
bFilename                VARCHAR(32),
bIsActive                VARCHAR(1) NOT NULL DEFAULT '1',
btID                     INTEGER(10) UNSIGNED NOT NULL DEFAULT 0,
uID                      INTEGER(10) UNSIGNED,
                 PRIMARY KEY (bID)
);

CREATE TABLE CollectionAttributeValues (
cID                      INTEGER(10) UNSIGNED NOT NULL DEFAULT 0,
cvID                     INTEGER(10) UNSIGNED NOT NULL DEFAULT 0,
akID                     INTEGER(10) UNSIGNED NOT NULL DEFAULT 0,
avID                     INTEGER(10) UNSIGNED NOT NULL DEFAULT 0,
                 PRIMARY KEY (cID, cvID, akID, avID)
);

CREATE TABLE CollectionVersionBlockPermissions (
cID                      INTEGER(10) UNSIGNED NOT NULL DEFAULT 0,
cvID                     INTEGER(10) UNSIGNED NOT NULL DEFAULT 1,
bID                      INTEGER(10) UNSIGNED NOT NULL DEFAULT 0,
gID                      INTEGER(10) UNSIGNED NOT NULL DEFAULT 0,
uID                      INTEGER(10) UNSIGNED NOT NULL DEFAULT 0,
cbgPermissions           VARCHAR(32),
                 PRIMARY KEY (cID, cvID, bID, gID, uID)
);

CREATE TABLE CollectionVersionBlocks (
cID                      INTEGER(10) UNSIGNED NOT NULL DEFAULT 0,
cvID                     INTEGER(10) UNSIGNED NOT NULL DEFAULT 1,
bID                      INTEGER(10) UNSIGNED NOT NULL DEFAULT 0,
arHandle                 VARCHAR(255) NOT NULL,
cbDisplayOrder           INTEGER(10) UNSIGNED NOT NULL DEFAULT 0,
isOriginal               VARCHAR(1) NOT NULL DEFAULT '0',
cbOverrideAreaPermissions TINYINT(1) NOT NULL DEFAULT 0,
cbIncludeAll             TINYINT(1) NOT NULL DEFAULT 0,
                 PRIMARY KEY (cID, cvID, bID, arHandle)
);

ALTER TABLE CollectionVersionBlocks ADD  INDEX cbIncludeAll  (cbIncludeAll);

CREATE TABLE CollectionVersionBlockStyles (
cID                      INTEGER(10) UNSIGNED NOT NULL DEFAULT 0,
cvID                     INTEGER(10) UNSIGNED NOT NULL DEFAULT 0,
bID                      INTEGER(10) UNSIGNED NOT NULL DEFAULT 0,
arHandle                 VARCHAR(255) NOT NULL,
csrID                    INTEGER(10) UNSIGNED NOT NULL DEFAULT 0,
                 PRIMARY KEY (cID, cvID, bID, arHandle)
);

CREATE TABLE CollectionVersionAreaLayouts (
cvalID                   INTEGER(10) UNSIGNED NOT NULL AUTO_INCREMENT,
cID                      INTEGER(10) UNSIGNED DEFAULT 0,
cvID                     INTEGER(10) UNSIGNED DEFAULT 0,
arHandle                 VARCHAR(255),
layoutID                 INTEGER(10) UNSIGNED NOT NULL DEFAULT 0,
position                 INTEGER(10) DEFAULT 1000,
areaNameNumber           INTEGER(10) UNSIGNED DEFAULT 0,
                 PRIMARY KEY (cvalID)
);

ALTER TABLE CollectionVersionAreaLayouts ADD  INDEX areaLayoutsIndex  (cID, cvID, arHandle);

CREATE TABLE CollectionVersionAreaStyles (
cID                      INTEGER(10) UNSIGNED NOT NULL DEFAULT 0,
cvID                     INTEGER(10) UNSIGNED NOT NULL DEFAULT 0,
arHandle                 VARCHAR(255) NOT NULL,
csrID                    INTEGER(10) UNSIGNED NOT NULL DEFAULT 0,
                 PRIMARY KEY (cID, cvID, arHandle)
);

CREATE TABLE CollectionVersions (
cID                      INTEGER(10) UNSIGNED NOT NULL DEFAULT 0,
cvID                     INTEGER(10) UNSIGNED NOT NULL DEFAULT 1,
cvName                   TEXT,
cvHandle                 VARCHAR(64),
cvDescription            TEXT,
cvDatePublic             DATETIME,
cvDateCreated            DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
cvComments               VARCHAR(255),
cvIsApproved             TINYINT(1) NOT NULL DEFAULT 0,
cvIsNew                  TINYINT(1) NOT NULL DEFAULT 0,
cvAuthorUID              INTEGER(10) UNSIGNED,
cvApproverUID            INTEGER(10) UNSIGNED,
cvActivateDatetime       DATETIME,
                 PRIMARY KEY (cID, cvID)
);

ALTER TABLE CollectionVersions ADD  INDEX cvIsApproved  (cvIsApproved);

ALTER TABLE CollectionVersions ADD  INDEX cvName  (cvName(128));

CREATE TABLE Collections (
cID                      INTEGER(10) UNSIGNED NOT NULL AUTO_INCREMENT,
cDateAdded               DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
cDateModified            DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
cHandle                  VARCHAR(255),
                 PRIMARY KEY (cID)
);

ALTER TABLE Collections ADD  INDEX cDateModified  (cDateModified);

ALTER TABLE Collections ADD  INDEX cDateAdded  (cDateAdded);

CREATE TABLE Config (
cfKey                    VARCHAR(64) NOT NULL,
timestamp                TIMESTAMP NOT NULL,
cfValue                  LONGTEXT,
uID                      INTEGER(10) UNSIGNED NOT NULL DEFAULT 0,
pkgID                    INTEGER(10) UNSIGNED NOT NULL DEFAULT 0,
                 PRIMARY KEY (cfKey, uID)
);

ALTER TABLE Config ADD  INDEX uID  (uID);

CREATE TABLE DashboardHomepage (
dbhID                    INTEGER(10) UNSIGNED NOT NULL AUTO_INCREMENT,
dbhModule                VARCHAR(255) NOT NULL,
dbhDisplayName           VARCHAR(255),
dbhDisplayOrder          INTEGER(10) UNSIGNED NOT NULL DEFAULT 0,
pkgID                    INTEGER(10) UNSIGNED NOT NULL DEFAULT 0,
                 PRIMARY KEY (dbhID)
);

CREATE TABLE DownloadStatistics (
dsID                     INTEGER(10) UNSIGNED NOT NULL AUTO_INCREMENT,
fID                      INTEGER(10) UNSIGNED NOT NULL,
fvID                     INTEGER(10) UNSIGNED NOT NULL,
uID                      INTEGER(10) UNSIGNED NOT NULL,
rcID                     INTEGER(10) UNSIGNED NOT NULL,
timestamp                TIMESTAMP NOT NULL,
                 PRIMARY KEY (dsID)
);

CREATE TABLE FileAttributeValues (
fID                      INTEGER(10) UNSIGNED NOT NULL DEFAULT 0,
fvID                     INTEGER(10) UNSIGNED NOT NULL DEFAULT 0,
akID                     INTEGER(10) UNSIGNED NOT NULL DEFAULT 0,
avID                     INTEGER(10) UNSIGNED NOT NULL DEFAULT 0,
                 PRIMARY KEY (fID, fvID, akID, avID)
);

CREATE TABLE FilePermissionFileTypes (
fsID                     INTEGER(10) UNSIGNED NOT NULL DEFAULT 0,
gID                      INTEGER(10) UNSIGNED NOT NULL DEFAULT 0,
uID                      INTEGER(10) UNSIGNED NOT NULL DEFAULT 0,
extension                VARCHAR(32) NOT NULL,
                 PRIMARY KEY (fsID, gID, uID, extension)
);

CREATE TABLE CustomStylePresets (
cspID                    INTEGER(10) UNSIGNED NOT NULL AUTO_INCREMENT,
cspName                  VARCHAR(255) NOT NULL,
csrID                    INTEGER(10) UNSIGNED NOT NULL DEFAULT 0,
                 PRIMARY KEY (cspID)
);

CREATE TABLE CustomStyleRules (
csrID                    INTEGER(10) UNSIGNED NOT NULL AUTO_INCREMENT,
css_id                   VARCHAR(128),
css_class                VARCHAR(128),
css_serialized           TEXT,
css_custom               TEXT,
                 PRIMARY KEY (csrID)
);

CREATE TABLE FilePermissions (
fID                      INTEGER(10) UNSIGNED NOT NULL DEFAULT 0,
gID                      INTEGER(10) UNSIGNED NOT NULL DEFAULT 0,
uID                      INTEGER(10) UNSIGNED NOT NULL DEFAULT 0,
canRead                  INTEGER(4) NOT NULL DEFAULT 0,
canWrite                 INTEGER(4) NOT NULL DEFAULT 0,
canAdmin                 INTEGER(4) NOT NULL DEFAULT 0,
canSearch                INTEGER(4) NOT NULL DEFAULT 0,
                 PRIMARY KEY (fID, gID, uID)
);

CREATE TABLE TaskPermissionUserGroups (
tpID                     INTEGER(10) UNSIGNED NOT NULL DEFAULT 0,
gID                      INTEGER(10) UNSIGNED NOT NULL DEFAULT 0,
uID                      INTEGER(10) UNSIGNED NOT NULL DEFAULT 0,
canRead                  INTEGER(1) NOT NULL DEFAULT 0,
                 PRIMARY KEY (tpID, gID, uID)
);

CREATE TABLE TaskPermissions (
tpID                     INTEGER(10) UNSIGNED NOT NULL AUTO_INCREMENT,
tpHandle                 VARCHAR(255),
tpName                   VARCHAR(255),
tpDescription            TEXT,
pkgID                    INTEGER(10) UNSIGNED NOT NULL DEFAULT 0,
                 PRIMARY KEY (tpID)
);

ALTER TABLE TaskPermissions ADD  UNIQUE INDEX tpHandle  (tpHandle);

CREATE TABLE FileSetPermissions (
fsID                     INTEGER(10) UNSIGNED NOT NULL DEFAULT 0,
gID                      INTEGER(10) UNSIGNED NOT NULL DEFAULT 0,
uID                      INTEGER(10) UNSIGNED NOT NULL DEFAULT 0,
canRead                  INTEGER(4),
canWrite                 INTEGER(4),
canAdmin                 INTEGER(4),
canAdd                   INTEGER(4),
canSearch                INTEGER(3) UNSIGNED,
                 PRIMARY KEY (fsID, gID, uID)
);

ALTER TABLE FileSetPermissions ADD  INDEX canRead  (canRead);

ALTER TABLE FileSetPermissions ADD  INDEX canWrite  (canWrite);

ALTER TABLE FileSetPermissions ADD  INDEX canAdmin  (canAdmin);

ALTER TABLE FileSetPermissions ADD  INDEX canSearch  (canSearch);

ALTER TABLE FileSetPermissions ADD  INDEX canAdd  (canAdd);

CREATE TABLE FileVersions (
fID                      INTEGER(10) UNSIGNED NOT NULL DEFAULT 0,
fvID                     INTEGER(10) UNSIGNED NOT NULL DEFAULT 0,
fvFilename               VARCHAR(255) NOT NULL,
fvPrefix                 VARCHAR(12),
fvGenericType            INTEGER(3) UNSIGNED NOT NULL DEFAULT 0,
fvSize                   INTEGER(20) UNSIGNED NOT NULL DEFAULT 0,
fvTitle                  VARCHAR(255),
fvDescription            TEXT,
fvTags                   VARCHAR(255),
fvIsApproved             INTEGER(10) UNSIGNED NOT NULL DEFAULT 1,
fvDateAdded              DATETIME,
fvApproverUID            INTEGER(10) UNSIGNED NOT NULL DEFAULT 0,
fvAuthorUID              INTEGER(10) UNSIGNED NOT NULL DEFAULT 0,
fvActivateDatetime       DATETIME,
fvHasThumbnail1          INTEGER(1) NOT NULL DEFAULT 0,
fvHasThumbnail2          INTEGER(1) NOT NULL DEFAULT 0,
fvHasThumbnail3          INTEGER(1) NOT NULL DEFAULT 0,
fvExtension              VARCHAR(32),
fvType                   INTEGER(10) UNSIGNED NOT NULL DEFAULT 0,
                 PRIMARY KEY (fID, fvID)
)ENGINE=MYISAM;

ALTER TABLE FileVersions ADD  INDEX fvExtension  (fvType);

ALTER TABLE FileVersions ADD  INDEX fvTitle  (fvTitle);

CREATE TABLE FileVersionLog (
fvlID                    INTEGER(10) UNSIGNED NOT NULL AUTO_INCREMENT,
fID                      INTEGER(10) UNSIGNED NOT NULL DEFAULT 0,
fvID                     INTEGER(10) UNSIGNED NOT NULL DEFAULT 0,
fvUpdateTypeID           INTEGER(3) UNSIGNED NOT NULL DEFAULT 0,
fvUpdateTypeAttributeID  INTEGER(10) UNSIGNED NOT NULL DEFAULT 0,
                 PRIMARY KEY (fvlID)
);

CREATE TABLE FileStorageLocations (
fslID                    INTEGER(10) UNSIGNED NOT NULL DEFAULT 0,
fslName                  VARCHAR(255) NOT NULL,
fslDirectory             VARCHAR(255) NOT NULL,
                 PRIMARY KEY (fslID)
);

CREATE TABLE Files (
fID                      INTEGER(10) UNSIGNED NOT NULL AUTO_INCREMENT,
fDateAdded               DATETIME,
uID                      INTEGER(10) UNSIGNED NOT NULL DEFAULT 0,
fslID                    INTEGER(10) UNSIGNED NOT NULL DEFAULT 0,
ocID                     INTEGER(10) UNSIGNED NOT NULL DEFAULT 0,
fOverrideSetPermissions  INTEGER(1) NOT NULL DEFAULT 0,
fPassword                VARCHAR(255),
                 PRIMARY KEY (fID, uID, fslID)
);

ALTER TABLE Files ADD  INDEX fOverrideSetPermissions  (fOverrideSetPermissions);

CREATE TABLE Groups (
gID                      INTEGER(10) UNSIGNED NOT NULL AUTO_INCREMENT,
gName                    VARCHAR(128) NOT NULL,
gDescription             VARCHAR(255) NOT NULL,
gUserExpirationIsEnabled INTEGER(1) NOT NULL DEFAULT 0,
gUserExpirationMethod    VARCHAR(12),
gUserExpirationSetDateTime DATETIME,
gUserExpirationInterval  INTEGER(10) UNSIGNED NOT NULL DEFAULT 0,
gUserExpirationAction    VARCHAR(20),
                 PRIMARY KEY (gID)
);

ALTER TABLE Groups ADD  UNIQUE INDEX gName  (gName);

CREATE TABLE Jobs (
jID                      INTEGER(10) UNSIGNED NOT NULL AUTO_INCREMENT,
jName                    VARCHAR(100) NOT NULL,
jDescription             VARCHAR(255) NOT NULL,
jDateInstalled           DATETIME,
jDateLastRun             DATETIME,
pkgID                    INTEGER(10) UNSIGNED NOT NULL DEFAULT 0,
jLastStatusText          VARCHAR(255),
jLastStatusCode          SMALLINT(4) NOT NULL DEFAULT 0,
jStatus                  VARCHAR(14) NOT NULL DEFAULT 'ENABLED',
jHandle                  VARCHAR(255) NOT NULL,
jNotUninstallable        SMALLINT(4) NOT NULL DEFAULT 0,
                 PRIMARY KEY (jID)
);

CREATE TABLE JobsLog (
jlID                     INTEGER(10) UNSIGNED NOT NULL AUTO_INCREMENT,
jID                      INTEGER(10) UNSIGNED NOT NULL,
jlMessage                VARCHAR(255) NOT NULL,
jlTimestamp              TIMESTAMP NOT NULL,
jlError                  INTEGER(10) NOT NULL DEFAULT 0,
                 PRIMARY KEY (jlID)
);

CREATE TABLE Layouts (
layoutID                 INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
layout_rows              INTEGER(5) NOT NULL DEFAULT 3,
layout_columns           INTEGER(3) NOT NULL DEFAULT 3,
spacing                  INTEGER(3) NOT NULL DEFAULT 3,
breakpoints              VARCHAR(255) NOT NULL DEFAULT '',
locked                   TINYINT(1) NOT NULL DEFAULT 0,
                 PRIMARY KEY (layoutID)
);

CREATE TABLE LayoutPresets (
lpID                     INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
lpName                   VARCHAR(128) NOT NULL,
layoutID                 INTEGER(10) UNSIGNED NOT NULL DEFAULT 0,
                 PRIMARY KEY (lpID)
);

ALTER TABLE LayoutPresets ADD  UNIQUE INDEX layoutID  (layoutID);

CREATE TABLE SystemNotifications (
snID                     INTEGER(10) UNSIGNED NOT NULL AUTO_INCREMENT,
snTypeID                 INTEGER(3) UNSIGNED NOT NULL DEFAULT 0,
snURL                    TEXT,
snURL2                   TEXT,
snDateTime               DATETIME NOT NULL,
snIsArchived             INTEGER(1) NOT NULL DEFAULT 0,
snIsNew                  INTEGER(1) NOT NULL DEFAULT 0,
snTitle                  VARCHAR(255),
snDescription            TEXT,
snBody                   TEXT,
                 PRIMARY KEY (snID)
);

CREATE TABLE Packages (
pkgID                    INTEGER(10) UNSIGNED NOT NULL AUTO_INCREMENT,
pkgName                  VARCHAR(255) NOT NULL,
pkgHandle                VARCHAR(64) NOT NULL,
pkgDescription           TEXT,
pkgDateInstalled         DATETIME NOT NULL,
pkgIsInstalled           TINYINT(1) NOT NULL DEFAULT 1,
pkgVersion               VARCHAR(32),
pkgAvailableVersion      VARCHAR(32),
                 PRIMARY KEY (pkgID)
);

ALTER TABLE Packages ADD  UNIQUE INDEX pkgHandle  (pkgHandle);

CREATE TABLE PagePaths (
ppID                     INTEGER(10) UNSIGNED NOT NULL AUTO_INCREMENT,
cID                      INTEGER(10) UNSIGNED DEFAULT 0,
cPath                    TEXT,
ppIsCanonical            VARCHAR(1) NOT NULL DEFAULT '1',
                 PRIMARY KEY (ppID)
);

ALTER TABLE PagePaths ADD  INDEX cID  (cID);

ALTER TABLE PagePaths ADD  INDEX ppIsCanonical  (ppIsCanonical);

ALTER TABLE PagePaths ADD  INDEX cPath (cPath(128));

CREATE TABLE PageSearchIndex (
cID                      INTEGER(10) UNSIGNED NOT NULL DEFAULT 0,
content                  TEXT,
cName                    VARCHAR(255),
cDescription             TEXT,
cPath                    TEXT,
cDatePublic              DATETIME,
cDateLastIndexed         DATETIME,
cDateLastSitemapped      DATETIME,
                 PRIMARY KEY (cID)
)ENGINE=MYISAM;

ALTER TABLE PageSearchIndex ADD  FULLTEXT INDEX cName  (cName);

ALTER TABLE PageSearchIndex ADD  FULLTEXT INDEX cDescription  (cDescription);

ALTER TABLE PageSearchIndex ADD  FULLTEXT INDEX content  (content);

ALTER TABLE PageSearchIndex ADD  FULLTEXT INDEX content2  (cName, cDescription, content);

ALTER TABLE PageSearchIndex ADD  INDEX cDateLastIndexed  (cDateLastIndexed);

ALTER TABLE PageSearchIndex ADD  INDEX cDateLastSitemapped  (cDateLastSitemapped);

CREATE TABLE PagePermissionPageTypes (
cID                      INTEGER(10) UNSIGNED NOT NULL DEFAULT 0,
gID                      INTEGER(10) UNSIGNED NOT NULL DEFAULT 0,
uID                      INTEGER(10) UNSIGNED NOT NULL DEFAULT 0,
ctID                     INTEGER(10) UNSIGNED NOT NULL DEFAULT 0,
                 PRIMARY KEY (cID, gID, uID, ctID)
);

CREATE TABLE PagePermissions (
cID                      INTEGER(10) UNSIGNED NOT NULL DEFAULT 0,
gID                      INTEGER(10) UNSIGNED NOT NULL DEFAULT 0,
uID                      INTEGER(10) UNSIGNED NOT NULL DEFAULT 0,
cgPermissions            VARCHAR(32),
cgStartDate              DATETIME,
cgEndDate                DATETIME,
                 PRIMARY KEY (cID, gID, uID)
);

CREATE TABLE PageStatistics (
pstID                    BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
cID                      INTEGER(10) UNSIGNED NOT NULL DEFAULT 0,
date                     DATE,
timestamp                TIMESTAMP NOT NULL,
uID                      INTEGER(10) UNSIGNED NOT NULL DEFAULT 0,
                 PRIMARY KEY (pstID)
)ENGINE=MYISAM;

ALTER TABLE PageStatistics ADD  INDEX cID  (cID);

ALTER TABLE PageStatistics ADD  INDEX date  (date);

ALTER TABLE PageStatistics ADD  INDEX uID  (uID);

CREATE TABLE PageThemes (
ptID                     INTEGER(10) UNSIGNED NOT NULL AUTO_INCREMENT,
ptHandle                 VARCHAR(64) NOT NULL,
ptName                   VARCHAR(255),
ptDescription            TEXT,
pkgID                    INTEGER(10) UNSIGNED NOT NULL DEFAULT 0,
                 PRIMARY KEY (ptID)
);

ALTER TABLE PageThemes ADD  UNIQUE INDEX ptHandle  (ptHandle);

CREATE TABLE PageThemeStyles (
ptID                     INTEGER(10) UNSIGNED NOT NULL DEFAULT 0,
ptsHandle                VARCHAR(128) NOT NULL,
ptsValue                 LONGTEXT,
ptsType                  VARCHAR(32) NOT NULL,
                 PRIMARY KEY (ptID, ptsHandle, ptsType)
);

CREATE TABLE PageTypeAttributes (
ctID                     INTEGER(10) UNSIGNED NOT NULL DEFAULT 0,
akID                     INTEGER(10) UNSIGNED NOT NULL DEFAULT 0,
                 PRIMARY KEY (ctID, akID)
);

CREATE TABLE PageTypes (
ctID                     INTEGER(10) UNSIGNED NOT NULL AUTO_INCREMENT,
ctHandle                 VARCHAR(32) NOT NULL,
ctIcon                   VARCHAR(128),
ctName                   VARCHAR(90) NOT NULL,
pkgID                    INTEGER(10) UNSIGNED NOT NULL DEFAULT 0,
                 PRIMARY KEY (ctID)
);

ALTER TABLE PageTypes ADD  UNIQUE INDEX ctHandle  (ctHandle);

CREATE TABLE Pages (
cID                      INTEGER(10) UNSIGNED NOT NULL DEFAULT 0,
ctID                     INTEGER(10) UNSIGNED NOT NULL DEFAULT 0,
cIsTemplate              VARCHAR(1) NOT NULL DEFAULT '0',
uID                      INTEGER(10) UNSIGNED,
cIsCheckedOut            TINYINT(1) NOT NULL DEFAULT 0,
cCheckedOutUID           INTEGER(10) UNSIGNED,
cCheckedOutDatetime      DATETIME,
cCheckedOutDatetimeLastEdit DATETIME,
cPendingAction           VARCHAR(6),
cPendingActionDatetime   DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
cPendingActionUID        INTEGER(10) UNSIGNED,
cPendingActionTargetCID  INTEGER(10) UNSIGNED,
cOverrideTemplatePermissions TINYINT(1) NOT NULL DEFAULT 1,
cInheritPermissionsFromCID INTEGER(10) UNSIGNED NOT NULL DEFAULT 0,
cInheritPermissionsFrom  VARCHAR(8) NOT NULL DEFAULT 'PARENT',
cFilename                VARCHAR(255),
cPointerID               INTEGER(10) UNSIGNED NOT NULL DEFAULT 0,
cPointerExternalLink     VARCHAR(255),
cPointerExternalLinkNewWindow TINYINT(1) NOT NULL DEFAULT 0,
cChildren                INTEGER(10) UNSIGNED NOT NULL DEFAULT 0,
cDisplayOrder            INTEGER(10) UNSIGNED NOT NULL DEFAULT 0,
cParentID                INTEGER(10) UNSIGNED NOT NULL DEFAULT 0,
pkgID                    INTEGER(10) UNSIGNED NOT NULL DEFAULT 0,
ptID                     INTEGER(10) UNSIGNED NOT NULL DEFAULT 0,
cCacheFullPageContent    INTEGER(4) NOT NULL DEFAULT -1,
cCacheFullPageContentOverrideLifetime VARCHAR(32) NOT NULL DEFAULT '0',
cCacheFullPageContentLifetimeCustom INTEGER(10) UNSIGNED NOT NULL DEFAULT 0,
                 PRIMARY KEY (cID)
);

ALTER TABLE Pages ADD  INDEX cParentID  (cParentID);

ALTER TABLE Pages ADD  INDEX cCheckedOutUID  (cCheckedOutUID);

ALTER TABLE Pages ADD  INDEX cPointerID  (cPointerID);

ALTER TABLE Pages ADD  INDEX uID  (uID);

ALTER TABLE Pages ADD  INDEX ctID  (ctID);

CREATE TABLE PileContents (
pcID                     INTEGER(10) UNSIGNED NOT NULL AUTO_INCREMENT,
pID                      INTEGER(10) UNSIGNED NOT NULL DEFAULT 0,
itemID                   INTEGER(10) UNSIGNED NOT NULL DEFAULT 0,
itemType                 VARCHAR(64) NOT NULL,
quantity                 INTEGER(10) UNSIGNED NOT NULL DEFAULT 1,
timestamp                TIMESTAMP NOT NULL,
displayOrder             INTEGER(10) UNSIGNED NOT NULL DEFAULT 0,
                 PRIMARY KEY (pcID)
);

CREATE TABLE Piles (
pID                      INTEGER(10) UNSIGNED NOT NULL AUTO_INCREMENT,
uID                      INTEGER(10) UNSIGNED,
isDefault                TINYINT(1) NOT NULL DEFAULT 0,
timestamp                TIMESTAMP NOT NULL,
name                     VARCHAR(255),
state                    VARCHAR(64) NOT NULL,
                 PRIMARY KEY (pID)
);

CREATE TABLE UserAttributeKeys (
akID                     INTEGER(10) UNSIGNED NOT NULL,
uakProfileDisplay        TINYINT(1) NOT NULL DEFAULT 0,
uakMemberListDisplay     TINYINT(1) NOT NULL DEFAULT 0,
uakProfileEdit           TINYINT(1) NOT NULL DEFAULT 1,
uakProfileEditRequired   TINYINT(1) NOT NULL DEFAULT 0,
uakRegisterEdit          TINYINT(1) NOT NULL DEFAULT 0,
uakRegisterEditRequired  TINYINT(1) NOT NULL DEFAULT 0,
displayOrder             INTEGER(10) UNSIGNED DEFAULT 0,
uakIsActive              TINYINT(1) NOT NULL DEFAULT 1,
                 PRIMARY KEY (akID)
);

CREATE TABLE UserAttributeValues (
uID                      INTEGER(10) UNSIGNED NOT NULL DEFAULT 0,
akID                     INTEGER(10) UNSIGNED NOT NULL DEFAULT 0,
avID                     INTEGER(10) UNSIGNED NOT NULL DEFAULT 0,
                 PRIMARY KEY (uID, akID, avID)
);

CREATE TABLE UserPrivateMessages (
msgID                    INTEGER(10) UNSIGNED NOT NULL AUTO_INCREMENT,
uAuthorID                INTEGER(10) UNSIGNED NOT NULL DEFAULT 0,
msgDateCreated           DATETIME NOT NULL,
msgSubject               VARCHAR(255) NOT NULL,
msgBody                  TEXT,
uToID                    INTEGER(10) UNSIGNED NOT NULL DEFAULT 0,
                 PRIMARY KEY (msgID)
);

CREATE TABLE UserPrivateMessagesTo (
msgID                    INTEGER(10) UNSIGNED NOT NULL DEFAULT 0,
uID                      INTEGER(10) UNSIGNED NOT NULL DEFAULT 0,
uAuthorID                INTEGER(10) UNSIGNED NOT NULL DEFAULT 0,
msgMailboxID             INTEGER(11) NOT NULL,
msgIsNew                 INTEGER(1) NOT NULL DEFAULT 0,
msgIsUnread              INTEGER(1) NOT NULL DEFAULT 0,
msgIsReplied             INTEGER(1) NOT NULL DEFAULT 0,
                 PRIMARY KEY (msgID, uID, uAuthorID)
);

ALTER TABLE UserPrivateMessagesTo ADD  INDEX uID  (uID);

ALTER TABLE UserPrivateMessagesTo ADD  INDEX uAuthorID  (uAuthorID);

ALTER TABLE UserPrivateMessagesTo ADD  INDEX msgFolderID  (msgMailboxID);

ALTER TABLE UserPrivateMessagesTo ADD  INDEX msgIsNew  (msgIsNew);

CREATE TABLE UserBannedIPs (
ipFrom                   INTEGER(10) UNSIGNED NOT NULL DEFAULT 0,
ipTo                     INTEGER(10) UNSIGNED NOT NULL DEFAULT 0,
banCode                  INTEGER(1) UNSIGNED NOT NULL DEFAULT 1,
expires                  INTEGER(10) UNSIGNED NOT NULL DEFAULT 0,
isManual                 INTEGER(1) NOT NULL DEFAULT 0,
                 PRIMARY KEY (ipFrom, ipTo)
);

ALTER TABLE UserBannedIPs ADD  INDEX ipFrom  (ipFrom);

ALTER TABLE UserBannedIPs ADD  INDEX ipTo  (ipTo);

CREATE TABLE UserGroups (
uID                      INTEGER(10) UNSIGNED NOT NULL DEFAULT 0,
gID                      INTEGER(10) UNSIGNED NOT NULL DEFAULT 0,
ugEntered                DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
type                     VARCHAR(64),
                 PRIMARY KEY (uID, gID)
);

ALTER TABLE UserGroups ADD  INDEX uID  (uID);

ALTER TABLE UserGroups ADD  INDEX gID  (gID);

CREATE TABLE UserValidationHashes (
uvhID                    INTEGER(10) UNSIGNED NOT NULL AUTO_INCREMENT,
uID                      INTEGER(10) UNSIGNED,
uHash                    VARCHAR(64) NOT NULL,
type                     INTEGER(4) UNSIGNED NOT NULL DEFAULT 0,
uDateGenerated           INTEGER(10) UNSIGNED NOT NULL DEFAULT 0,
uDateRedeemed            INTEGER(10) UNSIGNED NOT NULL DEFAULT 0,
                 PRIMARY KEY (uvhID)
);

CREATE TABLE Logs (
logID                    INTEGER(10) UNSIGNED NOT NULL AUTO_INCREMENT,
logType                  VARCHAR(64) NOT NULL,
timestamp                TIMESTAMP NOT NULL,
logText                  LONGTEXT,
logIsInternal            TINYINT(1) NOT NULL DEFAULT 0,
                 PRIMARY KEY (logID)
);

ALTER TABLE Logs ADD  INDEX logType  (logType);

ALTER TABLE Logs ADD  INDEX logIsInternal  (logIsInternal);

CREATE TABLE MailImporters (
miID                     INTEGER(10) UNSIGNED NOT NULL AUTO_INCREMENT,
miHandle                 VARCHAR(64) NOT NULL,
miServer                 VARCHAR(255),
miUsername               VARCHAR(255),
miPassword               VARCHAR(255),
miEncryption             VARCHAR(32),
miIsEnabled              INTEGER(1) NOT NULL DEFAULT 0,
miEmail                  VARCHAR(255),
miPort                   INTEGER(10) UNSIGNED NOT NULL DEFAULT 0,
pkgID                    INTEGER(10) UNSIGNED,
                 PRIMARY KEY (miID)
);

CREATE TABLE MailValidationHashes (
mvhID                    INTEGER(10) UNSIGNED NOT NULL AUTO_INCREMENT,
miID                     INTEGER(10) UNSIGNED NOT NULL DEFAULT 0,
email                    VARCHAR(255) NOT NULL,
mHash                    VARCHAR(128) NOT NULL,
mDateGenerated           INTEGER(10) UNSIGNED NOT NULL DEFAULT 0,
mDateRedeemed            INTEGER(10) UNSIGNED NOT NULL DEFAULT 0,
data                     TEXT,
                 PRIMARY KEY (mvhID)
);

CREATE TABLE UserOpenIDs (
uID                      INTEGER(10) UNSIGNED NOT NULL,
uOpenID                  VARCHAR(255) NOT NULL,
                 PRIMARY KEY (uOpenID)
);

ALTER TABLE UserOpenIDs ADD  INDEX uID  (uID);

CREATE TABLE Users (
uID                      INTEGER(10) UNSIGNED NOT NULL AUTO_INCREMENT,
uName                    VARCHAR(64) NOT NULL,
uEmail                   VARCHAR(64) NOT NULL,
uPassword                VARCHAR(255) NOT NULL,
uIsActive                VARCHAR(1) NOT NULL DEFAULT '0',
uIsValidated             TINYINT NOT NULL DEFAULT -1,
uIsFullRecord            TINYINT(1) NOT NULL DEFAULT 1,
uDateAdded               DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
uHasAvatar               TINYINT(1) NOT NULL DEFAULT 0,
uLastOnline              INTEGER(10) UNSIGNED NOT NULL DEFAULT 0,
uLastLogin               INTEGER(10) UNSIGNED NOT NULL DEFAULT 0,
uPreviousLogin           INTEGER(10) UNSIGNED NOT NULL DEFAULT 0,
uNumLogins               INTEGER(10) UNSIGNED NOT NULL DEFAULT 0,
uTimezone                VARCHAR(255),
                 PRIMARY KEY (uID)
);

ALTER TABLE Users ADD  UNIQUE INDEX uName  (uName);

CREATE TABLE UsersFriends (
ufID                     INTEGER(10) UNSIGNED NOT NULL AUTO_INCREMENT,
uID                      INTEGER(10) UNSIGNED,
status                   VARCHAR(64) NOT NULL,
friendUID                INTEGER(10) UNSIGNED,
uDateAdded               DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
                 PRIMARY KEY (ufID)
);

CREATE TABLE SignupRequests (
id                       INTEGER(11) NOT NULL AUTO_INCREMENT,
ipFrom                   INTEGER(10) UNSIGNED NOT NULL DEFAULT 0,
date_access              TIMESTAMP NOT NULL,
                 PRIMARY KEY (id)
);

ALTER TABLE SignupRequests ADD  INDEX index_ipFrom  (ipFrom);

CREATE TABLE FileSets (
fsID                     INTEGER(10) UNSIGNED NOT NULL AUTO_INCREMENT,
fsName                   VARCHAR(64) NOT NULL,
uID                      INTEGER(10) UNSIGNED NOT NULL DEFAULT 0,
fsType                   INTEGER(4) NOT NULL,
fsOverrideGlobalPermissions INTEGER(4),
                 PRIMARY KEY (fsID)
);

ALTER TABLE FileSets ADD  INDEX fsOverrideGlobalPermissions  (fsOverrideGlobalPermissions);

CREATE TABLE FileSetSavedSearches (
fsID                     INTEGER(10) UNSIGNED NOT NULL DEFAULT 0,
fsSearchRequest          TEXT,
fsResultColumns          TEXT,
                 PRIMARY KEY (fsID)
);

CREATE TABLE FileSetFiles (
fsfID                    INTEGER(10) UNSIGNED NOT NULL AUTO_INCREMENT,
fID                      INTEGER(10) UNSIGNED NOT NULL,
fsID                     INTEGER(10) UNSIGNED NOT NULL,
timestamp                TIMESTAMP NOT NULL,
fsDisplayOrder           INTEGER(10) UNSIGNED NOT NULL,
                 PRIMARY KEY (fsfID)
);

ALTER TABLE FileSetFiles ADD  INDEX fID  (fID);

ALTER TABLE FileSetFiles ADD  INDEX fsID  (fsID);

CREATE TABLE atBoolean (
avID                     INTEGER(10) UNSIGNED NOT NULL,
value                    TINYINT(1) NOT NULL DEFAULT 0,
                 PRIMARY KEY (avID)
);

CREATE TABLE atBooleanSettings (
akID                     INTEGER(10) UNSIGNED NOT NULL,
akCheckedByDefault       TINYINT(1) NOT NULL DEFAULT 0,
                 PRIMARY KEY (akID)
);

CREATE TABLE atDateTimeSettings (
akID                     INTEGER(10) UNSIGNED NOT NULL,
akDateDisplayMode        VARCHAR(255),
                 PRIMARY KEY (akID)
);

CREATE TABLE atDateTime (
avID                     INTEGER(10) UNSIGNED NOT NULL,
value                    DATETIME DEFAULT '0000-00-00 00:00:00',
                 PRIMARY KEY (avID)
);

CREATE TABLE atDefault (
avID                     INTEGER(10) UNSIGNED NOT NULL,
value                    LONGTEXT,
                 PRIMARY KEY (avID)
);

CREATE TABLE atFile (
avID                     INTEGER(10) UNSIGNED NOT NULL,
fID                      INTEGER(10) UNSIGNED NOT NULL,
                 PRIMARY KEY (avID)
);

CREATE TABLE atNumber (
avID                     INTEGER(10) UNSIGNED NOT NULL,
value                    NUMERIC(14,4) DEFAULT 0,
                 PRIMARY KEY (avID)
);

CREATE TABLE atSelectSettings (
akID                     INTEGER(10) UNSIGNED NOT NULL,
akSelectAllowMultipleValues TINYINT(1) NOT NULL DEFAULT 0,
akSelectOptionDisplayOrder VARCHAR(255) NOT NULL DEFAULT 'display_asc',
akSelectAllowOtherValues TINYINT(1) NOT NULL DEFAULT 0,
                 PRIMARY KEY (akID)
);

CREATE TABLE atTextareaSettings (
akID                     INTEGER(10) UNSIGNED NOT NULL DEFAULT 0,
akTextareaDisplayMode    VARCHAR(255),
                 PRIMARY KEY (akID)
);

CREATE TABLE atSelectOptions (
ID                       INTEGER(10) UNSIGNED NOT NULL AUTO_INCREMENT,
akID                     INTEGER(10) UNSIGNED,
value                    VARCHAR(255),
displayOrder             INTEGER(10) UNSIGNED,
isEndUserAdded           TINYINT(1) NOT NULL DEFAULT 0,
                 PRIMARY KEY (ID)
);

CREATE TABLE atSelectOptionsSelected (
avID                     INTEGER(10) UNSIGNED NOT NULL,
atSelectOptionID         INTEGER(10) UNSIGNED NOT NULL,
                 PRIMARY KEY (avID, atSelectOptionID)
);

ALTER TABLE atSelectOptionsSelected add index `atSelectOptionID` (atSelectOptionID);

CREATE TABLE atAddress (
avID                     INTEGER(10) UNSIGNED NOT NULL DEFAULT 0,
address1                 VARCHAR(255),
address2                 VARCHAR(255),
city                     VARCHAR(255),
state_province           VARCHAR(255),
country                  VARCHAR(4),
postal_code              VARCHAR(32),
                 PRIMARY KEY (avID)
);

CREATE TABLE atAddressCustomCountries (
atAddressCustomCountryID INTEGER(10) UNSIGNED NOT NULL AUTO_INCREMENT,
akID                     INTEGER(10) UNSIGNED NOT NULL DEFAULT 0,
country                  VARCHAR(5) NOT NULL,
                 PRIMARY KEY (atAddressCustomCountryID)
);

CREATE TABLE atAddressSettings (
akID                     INTEGER(10) UNSIGNED NOT NULL DEFAULT 0,
akHasCustomCountries     INTEGER(1) NOT NULL DEFAULT 0,
akDefaultCountry         VARCHAR(12),
                 PRIMARY KEY (akID)
);

CREATE TABLE btNavigation (
bID                      INTEGER UNSIGNED NOT NULL,
orderBy                  VARCHAR(255) DEFAULT 'alpha_asc',
displayPages             VARCHAR(255) DEFAULT 'top',
displayPagesCID          INTEGER UNSIGNED NOT NULL DEFAULT 1,
displayPagesIncludeSelf  TINYINT UNSIGNED NOT NULL DEFAULT 0,
displaySubPages          VARCHAR(255) DEFAULT 'none',
displaySubPageLevels     VARCHAR(255) DEFAULT 'none',
displaySubPageLevelsNum  SMALLINT UNSIGNED NOT NULL DEFAULT 0,
displayUnavailablePages  TINYINT UNSIGNED NOT NULL DEFAULT 0,
                 PRIMARY KEY (bID)
);

CREATE TABLE btDateNav (
bID                      INTEGER UNSIGNED NOT NULL,
num                      SMALLINT UNSIGNED NOT NULL,
cParentID                INTEGER UNSIGNED NOT NULL DEFAULT 1,
cThis                    TINYINT UNSIGNED NOT NULL DEFAULT 0,
ctID                     SMALLINT UNSIGNED,
flatDisplay              INTEGER DEFAULT 0,
defaultNode              VARCHAR(64) DEFAULT 'current_page',
truncateTitles           INTEGER DEFAULT 0,
truncateSummaries        INTEGER DEFAULT 0,
displayFeaturedOnly      INTEGER DEFAULT 0,
truncateChars            INTEGER DEFAULT 128,
truncateTitleChars       INTEGER DEFAULT 128,
showDescriptions         INTEGER DEFAULT 0,
                 PRIMARY KEY (bID)
);

CREATE TABLE btExternalForm (
bID                      INTEGER UNSIGNED NOT NULL,
filename                 VARCHAR(128),
                 PRIMARY KEY (bID)
);

CREATE TABLE btContentFile (
bID                      INTEGER UNSIGNED NOT NULL,
fID                      INTEGER UNSIGNED,
fileLinkText             VARCHAR(255),
filePassword             VARCHAR(255),
                 PRIMARY KEY (bID)
);

CREATE TABLE btFlashContent (
bID                      INTEGER UNSIGNED NOT NULL,
fID                      INTEGER UNSIGNED,
quality                  VARCHAR(255),
minVersion               VARCHAR(255),
                 PRIMARY KEY (bID)
);

CREATE TABLE btForm (
bID                      INTEGER UNSIGNED NOT NULL,
questionSetId            INTEGER UNSIGNED DEFAULT 0,
surveyName               VARCHAR(255),
thankyouMsg              TEXT,
notifyMeOnSubmission     TINYINT UNSIGNED NOT NULL DEFAULT 0,
recipientEmail           VARCHAR(255),
displayCaptcha           INTEGER DEFAULT 1,
redirectCID              INTEGER DEFAULT 0,
                 PRIMARY KEY (bID)
);

ALTER TABLE btForm ADD  INDEX questionSetIdForeign  (questionSetId);

CREATE TABLE btFormQuestions (
qID                      INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
msqID                    INTEGER UNSIGNED DEFAULT 0,
bID                      INTEGER UNSIGNED DEFAULT 0,
questionSetId            INTEGER UNSIGNED DEFAULT 0,
question                 VARCHAR(255),
inputType                VARCHAR(255),
options                  TEXT,
position                 INTEGER UNSIGNED DEFAULT 1000,
width                    INTEGER UNSIGNED DEFAULT 50,
height                   INTEGER UNSIGNED DEFAULT 3,
required                 INTEGER DEFAULT 0,
                 PRIMARY KEY (qID)
);

ALTER TABLE btFormQuestions ADD  INDEX questionSetId  (questionSetId);

ALTER TABLE btFormQuestions ADD  INDEX msqID  (msqID);

CREATE TABLE btFormAnswerSet (
asID                     INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
questionSetId            INTEGER UNSIGNED DEFAULT 0,
created                  TIMESTAMP,
uID                      INTEGER UNSIGNED DEFAULT 0,
                 PRIMARY KEY (asID)
);

CREATE TABLE btFormAnswers (
aID                      INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
asID                     INTEGER UNSIGNED DEFAULT 0,
msqID                    INTEGER UNSIGNED DEFAULT 0,
answer                   VARCHAR(255),
answerLong               TEXT,
                 PRIMARY KEY (aID)
);

CREATE TABLE btGoogleMap (
bID                      INTEGER UNSIGNED NOT NULL,
title                    VARCHAR(255),
api_key                  VARCHAR(255),
location                 VARCHAR(255),
latitude                 DOUBLE,
longitude                DOUBLE,
zoom                     INTEGER(8),
                 PRIMARY KEY (bID)
);

CREATE TABLE btGuestBook (
bID                      INTEGER UNSIGNED NOT NULL,
requireApproval          INTEGER DEFAULT 0,
title                    VARCHAR(100) DEFAULT 'Comments',
dateFormat               VARCHAR(100),
displayGuestBookForm     INTEGER DEFAULT 1,
displayCaptcha           INTEGER DEFAULT 1,
authenticationRequired   INTEGER DEFAULT 0,
notifyEmail              VARCHAR(100),
                 PRIMARY KEY (bID)
);

CREATE TABLE btGuestBookEntries (
bID                      INTEGER,
cID                      INTEGER DEFAULT 1,
entryID                  INTEGER NOT NULL AUTO_INCREMENT,
uID                      INTEGER DEFAULT 0,
commentText              LONGTEXT,
user_name                VARCHAR(100),
user_email               VARCHAR(100),
entryDate                TIMESTAMP,
approved                 INTEGER DEFAULT 1,
                 PRIMARY KEY (entryID)
);

ALTER TABLE btGuestBookEntries ADD  INDEX cID  (cID);

CREATE TABLE btContentLocal (
bID                      INTEGER UNSIGNED NOT NULL,
content                  LONGTEXT,
                 PRIMARY KEY (bID)
);

CREATE TABLE btContentImage (
bID                      INTEGER UNSIGNED NOT NULL,
fID                      INTEGER UNSIGNED DEFAULT 0,
fOnstateID               INTEGER UNSIGNED DEFAULT 0,
maxWidth                 INTEGER UNSIGNED DEFAULT 0,
maxHeight                INTEGER UNSIGNED DEFAULT 0,
externalLink             VARCHAR(255),
altText                  VARCHAR(255),
                 PRIMARY KEY (bID)
);

CREATE TABLE btFile (
bID                      INTEGER UNSIGNED NOT NULL,
filename                 VARCHAR(255),
origfilename             VARCHAR(255),
url                      VARCHAR(255),
type                     VARCHAR(32),
generictype              VARCHAR(32),
                 PRIMARY KEY (bID)
);

CREATE TABLE btNextPrevious (
bID                      INTEGER UNSIGNED NOT NULL,
linkStyle                VARCHAR(32),
nextLabel                VARCHAR(128),
previousLabel            VARCHAR(128),
showArrows               INTEGER DEFAULT 1,
loopSequence             INTEGER DEFAULT 1,
excludeSystemPages       INTEGER DEFAULT 1,
                 PRIMARY KEY (bID)
);

CREATE TABLE btPageList (
bID                      INTEGER UNSIGNED NOT NULL,
num                      SMALLINT UNSIGNED NOT NULL,
orderBy                  VARCHAR(32),
cParentID                INTEGER UNSIGNED NOT NULL DEFAULT 1,
cThis                    TINYINT UNSIGNED NOT NULL DEFAULT 0,
paginate                 TINYINT UNSIGNED NOT NULL DEFAULT 0,
displayAliases           TINYINT UNSIGNED NOT NULL DEFAULT 1,
ctID                     SMALLINT UNSIGNED,
rss                      INTEGER DEFAULT 0,
rssTitle                 VARCHAR(255),
rssDescription           LONGTEXT,
truncateSummaries        INTEGER DEFAULT 0,
displayFeaturedOnly      INTEGER DEFAULT 0,
truncateChars            INTEGER DEFAULT 128,
                 PRIMARY KEY (bID)
);

CREATE TABLE btRssDisplay (
bID                      INTEGER UNSIGNED NOT NULL,
title                    VARCHAR(255),
url                      VARCHAR(255),
dateFormat               VARCHAR(100),
itemsToDisplay           INTEGER UNSIGNED DEFAULT 5,
showSummary              TINYINT UNSIGNED NOT NULL DEFAULT 1,
launchInNewWindow        TINYINT UNSIGNED NOT NULL DEFAULT 1,
                 PRIMARY KEY (bID)
);

CREATE TABLE btSearch (
bID                      INTEGER UNSIGNED NOT NULL,
title                    VARCHAR(255),
buttonText               VARCHAR(128),
baseSearchPath           VARCHAR(255),
resultsURL               VARCHAR(255),
                 PRIMARY KEY (bID)
);

CREATE TABLE btSlideshow (
bID                      INTEGER UNSIGNED NOT NULL,
fsID                     INTEGER UNSIGNED,
playback                 VARCHAR(50),
duration                 INTEGER UNSIGNED,
fadeDuration             INTEGER UNSIGNED,
                 PRIMARY KEY (bID)
);

CREATE TABLE btSlideshowImg (
slideshowImgId           INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
bID                      INTEGER UNSIGNED,
fID                      INTEGER UNSIGNED,
url                      VARCHAR(255),
duration                 INTEGER UNSIGNED,
fadeDuration             INTEGER UNSIGNED,
groupSet                 INTEGER UNSIGNED,
position                 INTEGER UNSIGNED,
imgHeight                INTEGER UNSIGNED,
                 PRIMARY KEY (slideshowImgId)
);

CREATE TABLE btSurvey (
bID                      INTEGER UNSIGNED NOT NULL,
question                 VARCHAR(255) DEFAULT '',
requiresRegistration     INTEGER DEFAULT 0,
                 PRIMARY KEY (bID)
);

CREATE TABLE btSurveyOptions (
optionID                 INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
bID                      INTEGER,
optionName               VARCHAR(255),
displayOrder             INTEGER DEFAULT 0,
                 PRIMARY KEY (optionID)
);

CREATE TABLE btSurveyResults (
resultID                 INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
optionID                 INTEGER UNSIGNED DEFAULT 0,
uID                      INTEGER UNSIGNED DEFAULT 0,
bID                      INTEGER,
cID                      INTEGER,
ipAddress                VARCHAR(128),
timestamp                TIMESTAMP,
                 PRIMARY KEY (resultID)
);

CREATE TABLE btTags (
bID                      INTEGER UNSIGNED NOT NULL,
title                    VARCHAR(255),
targetCID                INTEGER,
                 PRIMARY KEY (bID)
);

CREATE TABLE btVideo (
bID                      INTEGER UNSIGNED NOT NULL,
fID                      INTEGER UNSIGNED,
width                    INTEGER UNSIGNED,
height                   INTEGER UNSIGNED,
                 PRIMARY KEY (bID)
);

CREATE TABLE btYouTube (
bID                      INTEGER UNSIGNED NOT NULL,
title                    VARCHAR(255),
videoURL                 VARCHAR(255),
                 PRIMARY KEY (bID)
);


CREATE TABLE IF NOT EXISTS `CollectionSearchIndexAttributes` (
  `cID` int(11) unsigned NOT NULL default '0',
  `ak_meta_title` text,
  `ak_meta_description` text,
  `ak_meta_keywords` text,
  `ak_exclude_nav` tinyint(4) default '0',
  `ak_exclude_page_list` tinyint(4) default '0',
  `ak_header_extra_content` text,
  `ak_exclude_search_index` tinyint(4) default '0',
  `ak_exclude_sitemapxml` tinyint(4) default '0',
  `ak_tags` text,
  PRIMARY KEY  (`cID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `FileSearchIndexAttributes` (
  `fID` int(11) unsigned NOT NULL default '0',
  `ak_width` decimal(14,4) default '0.0000',
  `ak_height` decimal(14,4) default '0.0000',
  PRIMARY KEY  (`fID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE UserSearchIndexAttributes (
uID                      INTEGER(11) UNSIGNED NOT NULL DEFAULT 0,
                 PRIMARY KEY (uID)
);