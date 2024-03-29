<?php
$BEAUT_PATH = realpath(".")."/Services/COPage/syntax_highlight/php";
if (!isset ($BEAUT_PATH)) return;
require_once("$BEAUT_PATH/Beautifier/HFile.php");
  class HFile_mssql7 extends HFile{
   function HFile_mssql7(){
     $this->HFile();	
/*************************************/
// Beautifier Highlighting Configuration File 
// SQL
/*************************************/
// Flags

$this->nocase            	= "1";
$this->notrim            	= "0";
$this->perl              	= "0";

// Colours

$this->colours        	= array("blue", "purple", "gray", "brown");
$this->quotecolour       	= "blue";
$this->blockcommentcolour	= "green";
$this->linecommentcolour 	= "green";

// Indent Strings

$this->indent            	= array("BEGIN");
$this->unindent          	= array("END");

// String characters and delimiters

$this->stringchars       	= array("\"", "'");
$this->delimiters        	= array("!", "@", "%", "^", "&", "*", "(", ")", "-", "+", "=", "|", "\\", "/", "[", "]", ";", "\"", "'", "<", ">", " ", ",", "	", ".");
$this->escchar           	= "";

// Comment settings

$this->linecommenton     	= array("");
$this->blockcommenton    	= array("/*");
$this->blockcommentoff   	= array("*/");

// Keywords (keyword mapping to colour number)

$this->keywords          	= array(
			"add" => "1", 
			"all" => "1", 
			"alter" => "1", 
			"and" => "1", 
			"any" => "1", 
			"as" => "1", 
			"asc" => "1", 
			"authorization" => "1", 
			"avg" => "1", 
			"backup" => "1", 
			"begin" => "1", 
			"between" => "1", 
			"break" => "1", 
			"browse" => "1", 
			"bulk" => "1", 
			"by" => "1", 
			"cascade" => "1", 
			"case" => "1", 
			"check" => "1", 
			"checkpoint" => "1", 
			"close" => "1", 
			"clustered" => "1", 
			"coalesce" => "1", 
			"column" => "1", 
			"commit" => "1", 
			"committed" => "1", 
			"compute" => "1", 
			"confirm" => "1", 
			"constraint" => "1", 
			"contains" => "1", 
			"containstable" => "1", 
			"continue" => "1", 
			"controlrow" => "1", 
			"convert" => "1", 
			"count" => "1", 
			"create" => "1", 
			"cross" => "1", 
			"current" => "1", 
			"current_date" => "1", 
			"current_time" => "1", 
			"current_timestamp" => "1", 
			"current_user" => "1", 
			"cursor" => "1", 
			"database" => "1", 
			"dbcc" => "1", 
			"deallocate" => "1", 
			"declare" => "1", 
			"default" => "1", 
			"delete" => "1", 
			"deny" => "1", 
			"desc" => "1", 
			"disk" => "1", 
			"distinct" => "1", 
			"distributed" => "1", 
			"double" => "1", 
			"drop" => "1", 
			"dummy" => "1", 
			"dump" => "1", 
			"else" => "1", 
			"end" => "1", 
			"errlvl" => "1", 
			"errorexit" => "1", 
			"escape" => "1", 
			"except" => "1", 
			"exec" => "1", 
			"execute" => "1", 
			"exists" => "1", 
			"exit" => "1", 
			"fetch" => "1", 
			"file" => "1", 
			"fillfactor" => "1", 
			"floppy" => "1", 
			"for" => "1", 
			"foreign" => "1", 
			"freetext" => "1", 
			"freetexttable" => "1", 
			"from" => "1", 
			"full" => "1", 
			"go" => "1", 
			"goto" => "1", 
			"grant" => "1", 
			"group" => "1", 
			"having" => "1", 
			"holdlock" => "1", 
			"identity" => "1", 
			"identity_insert" => "1", 
			"identitycol" => "1", 
			"if" => "1", 
			"in" => "1", 
			"index" => "1", 
			"inner" => "1", 
			"insert" => "1", 
			"intersect" => "1", 
			"into" => "1", 
			"is" => "1", 
			"isolation" => "1", 
			"join" => "1", 
			"key" => "1", 
			"kill" => "1", 
			"left" => "1", 
			"level" => "1", 
			"like" => "1", 
			"lineno" => "1", 
			"load" => "1", 
			"max" => "1", 
			"min" => "1", 
			"mirrorexit" => "1", 
			"national" => "1", 
			"nocheck" => "1", 
			"nonclustered" => "1", 
			"not" => "1", 
			"null" => "1", 
			"nullif" => "1", 
			"of" => "1", 
			"off" => "1", 
			"offsets" => "1", 
			"on" => "1", 
			"once" => "1", 
			"only" => "1", 
			"open" => "1", 
			"opendatasource" => "1", 
			"openquery" => "1", 
			"openrowset" => "1", 
			"option" => "1", 
			"or" => "1", 
			"order" => "1", 
			"outer" => "1", 
			"over" => "1", 
			"percent" => "1", 
			"perm" => "1", 
			"permanent" => "1", 
			"pipe" => "1", 
			"plan" => "1", 
			"precision" => "1", 
			"prepare" => "1", 
			"primary" => "1", 
			"print" => "1", 
			"privileges" => "1", 
			"proc" => "1", 
			"procedure" => "1", 
			"processexit" => "1", 
			"public" => "1", 
			"raiserror" => "1", 
			"read" => "1", 
			"readtext" => "1", 
			"reconfigure" => "1", 
			"references" => "1", 
			"repeatable" => "1", 
			"replication" => "1", 
			"restore" => "1", 
			"restrict" => "1", 
			"return" => "1", 
			"revoke" => "1", 
			"right" => "1", 
			"rollback" => "1", 
			"rowcount" => "1", 
			"rowguidcol" => "1", 
			"rule" => "1", 
			"save" => "1", 
			"schema" => "1", 
			"select" => "1", 
			"serializable" => "1", 
			"session_user" => "1", 
			"set" => "1", 
			"setuser" => "1", 
			"shutdown" => "1", 
			"some" => "1", 
			"statistics" => "1", 
			"sum" => "1", 
			"system_user" => "1", 
			"table" => "1", 
			"tape" => "1", 
			"temp" => "1", 
			"temporary" => "1", 
			"textsize" => "1", 
			"then" => "1", 
			"to" => "1", 
			"top" => "1", 
			"tran" => "1", 
			"transaction" => "1", 
			"trigger" => "1", 
			"truncate" => "1", 
			"tsequal" => "1", 
			"uncommitted" => "1", 
			"union" => "1", 
			"unique" => "1", 
			"update" => "1", 
			"updatetext" => "1", 
			"use" => "1", 
			"user" => "1", 
			"values" => "1", 
			"varying" => "1", 
			"view" => "1", 
			"waitfor" => "1", 
			"when" => "1", 
			"where" => "1", 
			"while" => "1", 
			"with" => "1", 
			"work" => "1", 
			"writetext" => "1", 
			"binary" => "2", 
			"bit" => "2", 
			"char" => "2", 
			"character" => "2", 
			"datetime" => "2", 
			"dec" => "2", 
			"decimal" => "2", 
			"float" => "2", 
			"image" => "2", 
			"int" => "2", 
			"integer" => "2", 
			"money" => "2", 
			"nchar" => "2", 
			"ntext" => "2", 
			"numeric" => "2", 
			"nvarchar" => "2", 
			"real" => "2", 
			"smalldatetime" => "2", 
			"smallint" => "2", 
			"smallmoney" => "2", 
			"text" => "2", 
			"timestamp" => "2", 
			"tinyint" => "2", 
			"uniqueidentifier" => "2", 
			"varbinary" => "2", 
			"varchar" => "2", 
			"sp_abort_xact" => "3", 
			"sp_add_agent_parameter" => "3", 
			"sp_add_agent_profile" => "3", 
			"sp_add_server_sortinfo" => "3", 
			"sp_addalias" => "3", 
			"sp_addapprole" => "3", 
			"sp_addarticle" => "3", 
			"sp_adddistpublisher" => "3", 
			"sp_adddistributiondb" => "3", 
			"sp_adddistributor" => "3", 
			"sp_addextendedproc" => "3", 
			"sp_addgroup" => "3", 
			"sp_addlinkedserver" => "3", 
			"sp_addlinkedsrvlogin" => "3", 
			"sp_addlogin" => "3", 
			"sp_addmergearticle" => "3", 
			"sp_addmergefilter" => "3", 
			"sp_addmergepublication" => "3", 
			"sp_addmergepullsubscription" => "3", 
			"sp_addmergepullsubscription_agent" => "3", 
			"sp_addmergesubscription" => "3", 
			"sp_addmessage" => "3", 
			"sp_addpublication" => "3", 
			"sp_addpublication_snaHFileot" => "3", 
			"sp_addpublisher" => "3", 
			"sp_addpullsubscription" => "3", 
			"sp_addpullsubscription_agent" => "3", 
			"sp_addremotelogin" => "3", 
			"sp_addrole" => "3", 
			"sp_addrolemember" => "3", 
			"sp_addserver" => "3", 
			"sp_addsrvrolemember" => "3", 
			"sp_addsubscriber" => "3", 
			"sp_addsubscriber_schedule" => "3", 
			"sp_addsubscription" => "3", 
			"sp_addsynctriggers" => "3", 
			"sp_addtype" => "3", 
			"sp_addumpdevice" => "3", 
			"sp_adduser" => "3", 
			"sp_altermessage" => "3", 
			"sp_approlepassword" => "3", 
			"sp_article_validation" => "3", 
			"sp_articlecolumn" => "3", 
			"sp_articlefilter" => "3", 
			"sp_articlesynctranprocs" => "3", 
			"sp_articleview" => "3", 
			"sp_attach_db" => "3", 
			"sp_attach_single_file_db" => "3", 
			"sp_autostats" => "3", 
			"sp_bindefault" => "3", 
			"sp_bindrule" => "3", 
			"sp_bindsession" => "3", 
			"sp_blockcnt" => "3", 
			"sp_catalogs" => "3", 
			"sp_catalogs_rowset" => "3", 
			"sp_certify_removable" => "3", 
			"sp_change_subscription_properties" => "3", 
			"sp_change_users_login" => "3", 
			"sp_changearticle" => "3", 
			"sp_changedbowner" => "3", 
			"sp_changedistpublisher" => "3", 
			"sp_changedistributiondb" => "3", 
			"sp_changedistributor_password" => "3", 
			"sp_changedistributor_property" => "3", 
			"sp_changegroup" => "3", 
			"sp_changemergearticle" => "3", 
			"sp_changemergefilter" => "3", 
			"sp_changemergepublication" => "3", 
			"sp_changemergepullsubscription" => "3", 
			"sp_changemergesubscription" => "3", 
			"sp_changeobjectowner" => "3", 
			"sp_changepublication" => "3", 
			"sp_changesubscriber" => "3", 
			"sp_changesubscriber_schedule" => "3", 
			"sp_changesubscription" => "3", 
			"sp_changesubstatus" => "3", 
			"sp_check_for_sync_trigger" => "3", 
			"sp_check_removable" => "3", 
			"sp_check_removable_sysusers" => "3", 
			"sp_check_sync_trigger" => "3", 
			"sp_checknames" => "3", 
			"sp_cleanupwebtask" => "3", 
			"sp_column_privileges" => "3", 
			"sp_column_privileges_ex" => "3", 
			"sp_column_privileges_rowset" => "3", 
			"sp_columns" => "3", 
			"sp_columns_ex" => "3", 
			"sp_columns_rowset" => "3", 
			"sp_commit_xact" => "3", 
			"sp_configure" => "3", 
			"sp_create_removable" => "3", 
			"sp_createorphan" => "3", 
			"sp_createstats" => "3", 
			"sp_cursor" => "3", 
			"sp_cursor_list" => "3", 
			"sp_cursorclose" => "3", 
			"sp_cursorexecute" => "3", 
			"sp_cursorfetch" => "3", 
			"sp_cursoropen" => "3", 
			"sp_cursoroption" => "3", 
			"sp_cursorprepare" => "3", 
			"sp_cursorunprepare" => "3", 
			"sp_databases" => "3", 
			"sp_datatype_info" => "3", 
			"sp_db_upgrade" => "3", 
			"sp_dbcmptlevel" => "3", 
			"sp_dbfixedrolepermission" => "3", 
			"sp_dboption" => "3", 
			"sp_dbremove" => "3", 
			"sp_ddopen" => "3", 
			"sp_defaultdb" => "3", 
			"sp_defaultlanguage" => "3", 
			"sp_deletemergeconflictrow" => "3", 
			"sp_denylogin" => "3", 
			"sp_depends" => "3", 
			"sp_describe_cursor" => "3", 
			"sp_describe_cursor_columns" => "3", 
			"sp_describe_cursor_tables" => "3", 
			"sp_detach_db" => "3", 
			"sp_diskdefault" => "3", 
			"sp_distcounters" => "3", 
			"sp_drop_agent_parameter" => "3", 
			"sp_drop_agent_profile" => "3", 
			"sp_dropalias" => "3", 
			"sp_dropapprole" => "3", 
			"sp_droparticle" => "3", 
			"sp_dropdevice" => "3", 
			"sp_dropdistpublisher" => "3", 
			"sp_dropdistributiondb" => "3", 
			"sp_dropdistributor" => "3", 
			"sp_dropextendedproc" => "3", 
			"sp_dropgroup" => "3", 
			"sp_droplinkedsrvlogin" => "3", 
			"sp_droplogin" => "3", 
			"sp_dropmergearticle" => "3", 
			"sp_dropmergefilter" => "3", 
			"sp_dropmergepublication" => "3", 
			"sp_dropmergepullsubscription" => "3", 
			"sp_dropmergesubscription" => "3", 
			"sp_dropmessage" => "3", 
			"sp_droporphans" => "3", 
			"sp_droppublication" => "3", 
			"sp_droppublisher" => "3", 
			"sp_droppullsubscription" => "3", 
			"sp_dropremotelogin" => "3", 
			"sp_droprole" => "3", 
			"sp_droprolemember" => "3", 
			"sp_dropserver" => "3", 
			"sp_dropsrvrolemember" => "3", 
			"sp_dropsubscriber" => "3", 
			"sp_dropsubscription" => "3", 
			"sp_droptype" => "3", 
			"sp_dropuser" => "3", 
			"sp_dropwebtask" => "3", 
			"sp_dsninfo" => "3", 
			"sp_enumcodepages" => "3", 
			"sp_enumcustomresolvers" => "3", 
			"sp_enumdsn" => "3", 
			"sp_enumfullsubscribers" => "3", 
			"sp_enumoledbdatasources" => "3", 
			"sp_execute" => "3", 
			"sp_executesql" => "3", 
			"sp_fallback_MS_sel_fb_svr" => "3", 
			"sp_fetchshowcmdsinput" => "3", 
			"sp_fixindex" => "3", 
			"sp_fkeys" => "3", 
			"sp_foreign_keys_rowset" => "3", 
			"sp_foreignkeys" => "3", 
			"sp_fulltext_catalog" => "3", 
			"sp_fulltext_column" => "3", 
			"sp_fulltext_database" => "3", 
			"sp_fulltext_getdata" => "3", 
			"sp_fulltext_service" => "3", 
			"sp_fulltext_table" => "3", 
			"sp_generatefilters" => "3", 
			"sp_get_distributor" => "3", 
			"sp_getarticlepkcolbitmap" => "3", 
			"sp_getbindtoken" => "3", 
			"sp_GetMBCSCharLen" => "3", 
			"sp_getmergedeletetype" => "3", 
			"sp_gettypestring" => "3", 
			"sp_grant_publication_access" => "3", 
			"sp_grantdbaccess" => "3", 
			"sp_grantlogin" => "3", 
			"sp_help" => "3", 
			"sp_help_agent_default" => "3", 
			"sp_help_agent_parameter" => "3", 
			"sp_help_agent_profile" => "3", 
			"sp_help_fulltext_catalogs" => "3", 
			"sp_help_fulltext_catalogs_cursor" => "3", 
			"sp_help_fulltext_columns" => "3", 
			"sp_help_fulltext_columns_cursor" => "3", 
			"sp_help_fulltext_tables" => "3", 
			"sp_help_fulltext_tables_cursor" => "3", 
			"sp_help_publication_access" => "3", 
			"sp_helpallowmerge_publication" => "3", 
			"sp_helparticle" => "3", 
			"sp_helparticlecolumns" => "3", 
			"sp_helpconstraint" => "3", 
			"sp_helpdb" => "3", 
			"sp_helpdbfixedrole" => "3", 
			"sp_helpdevice" => "3", 
			"sp_helpdistpublisher" => "3", 
			"sp_helpdistributiondb" => "3", 
			"sp_helpdistributor" => "3", 
			"sp_helpdistributor_properties" => "3", 
			"sp_helpextendedproc" => "3", 
			"sp_helpfile" => "3", 
			"sp_helpfilegroup" => "3", 
			"sp_helpgroup" => "3", 
			"sp_helpindex" => "3", 
			"sp_helplanguage" => "3", 
			"sp_helplog" => "3", 
			"sp_helplogins" => "3", 
			"sp_helpmergearticle" => "3", 
			"sp_helpmergearticleconflicts" => "3", 
			"sp_helpmergeconflictrows" => "3", 
			"sp_helpmergedeleteconflictrows" => "3", 
			"sp_helpmergefilter" => "3", 
			"sp_helpmergepublication" => "3", 
			"sp_helpmergepullsubscription" => "3", 
			"sp_helpmergesubscription" => "3", 
			"sp_helpntgroup" => "3", 
			"sp_helppublication" => "3", 
			"sp_helppublication_snaHFileot" => "3", 
			"sp_helppublicationsync" => "3", 
			"sp_helppullsubscription" => "3", 
			"sp_helpremotelogin" => "3", 
			"sp_helpreplicationdb" => "3", 
			"sp_helpreplicationdboption" => "3", 
			"sp_helpreplicationoption" => "3", 
			"sp_helprole" => "3", 
			"sp_helprolemember" => "3", 
			"sp_helprotect" => "3", 
			"sp_helpserver" => "3", 
			"sp_helpsort" => "3", 
			"sp_helpsql" => "3", 
			"sp_helpsrvrole" => "3", 
			"sp_helpsrvrolemember" => "3", 
			"sp_helpstartup" => "3", 
			"sp_helpsubscriber" => "3", 
			"sp_helpsubscriberinfo" => "3", 
			"sp_helpsubscription" => "3", 
			"sp_helpsubscription_properties" => "3", 
			"sp_helptext" => "3", 
			"sp_helptrigger" => "3", 
			"sp_helpuser" => "3", 
			"sp_indexes" => "3", 
			"sp_indexes_rowset" => "3", 
			"sp_indexoption" => "3", 
			"sp_isarticlecolbitset" => "3", 
			"sp_IsMBCSLeadByte" => "3", 
			"sp_link_publication" => "3", 
			"sp_linkedservers" => "3", 
			"sp_linkedservers_rowset" => "3", 
			"sp_lock" => "3", 
			"sp_lockinfo" => "3", 
			"sp_logdevice" => "3", 
			"sp_makestartup" => "3", 
			"sp_makewebtask" => "3", 
			"sp_mergedummyupdate" => "3", 
			"sp_mergesubscription_cleanup" => "3", 
			"sp_mergesubscriptioncleanup" => "3", 
			"sp_monitor" => "3", 
			"sp_MS_marksystemobject" => "3", 
			"sp_MS_replication_installed" => "3", 
			"sp_MS_upd_sysobj_category" => "3", 
			"sp_MSactivate_auto_sub" => "3", 
			"sp_MSadd_distributor_alerts_and_responses" => "3", 
			"sp_MSadd_mergereplcommand" => "3", 
			"sp_MSadd_repl_job" => "3", 
			"sp_MSaddanonymousreplica" => "3", 
			"sp_MSaddarticletocontents" => "3", 
			"sp_MSaddexecarticle" => "3", 
			"sp_MSaddguidcolumn" => "3", 
			"sp_MSaddguidindex" => "3", 
			"sp_MSaddinitialarticle" => "3", 
			"sp_MSaddinitialpublication" => "3", 
			"sp_MSaddinitialsubscription" => "3", 
			"sp_MSaddlogin_implicit_ntlogin" => "3", 
			"sp_MSaddmergepub_snaHFileot" => "3", 
			"sp_MSaddmergetriggers" => "3", 
			"sp_MSaddpub_snaHFileot" => "3", 
			"sp_MSaddpubtocontents" => "3", 
			"sp_MSaddupdatetrigger" => "3", 
			"sp_MSadduser_implicit_ntlogin" => "3", 
			"sp_MSarticlecleanup" => "3", 
			"sp_MSarticletextcol" => "3", 
			"sp_MSbelongs" => "3", 
			"sp_MSchange_priority" => "3", 
			"sp_MSchangearticleresolver" => "3", 
			"sp_MScheck_agent_instance" => "3", 
			"sp_MScheck_uid_owns_anything" => "3", 
			"sp_MScheckatpublisher" => "3", 
			"sp_MScheckexistsgeneration" => "3", 
			"sp_MScheckmetadatamatch" => "3", 
			"sp_MScleanup_subscription" => "3", 
			"sp_MScleanuptask" => "3", 
			"sp_MScontractsubsnb" => "3", 
			"sp_MScreate_dist_tables" => "3", 
			"sp_MScreate_distributor_tables" => "3", 
			"sp_MScreate_mergesystables" => "3", 
			"sp_MScreate_pub_tables" => "3", 
			"sp_MScreate_replication_checkup_agent" => "3", 
			"sp_MScreate_replication_status_table" => "3", 
			"sp_MScreate_sub_tables" => "3", 
			"sp_MScreateglobalreplica" => "3", 
			"sp_MScreateretry" => "3", 
			"sp_MSdbuseraccess" => "3", 
			"sp_MSdbuserpriv" => "3", 
			"sp_MSdeletecontents" => "3", 
			"sp_MSdeletepushagent" => "3", 
			"sp_MSdeleteretry" => "3", 
			"sp_MSdelrow" => "3", 
			"sp_MSdelsubrows" => "3", 
			"sp_MSdependencies" => "3", 
			"sp_MSdoesfilterhaveparent" => "3", 
			"sp_MSdrop_6x_replication_agent" => "3", 
			"sp_MSdrop_distributor_alerts_and_responses" => "3", 
			"sp_MSdrop_mergesystables" => "3", 
			"sp_MSdrop_object" => "3", 
			"sp_MSdrop_pub_tables" => "3", 
			"sp_MSdrop_replcom" => "3", 
			"sp_MSdrop_repltran" => "3", 
			"sp_MSdrop_rladmin" => "3", 
			"sp_MSdrop_rlcore" => "3", 
			"sp_MSdrop_rlrecon" => "3", 
			"sp_MSdroparticleprocs" => "3", 
			"sp_MSdroparticletombstones" => "3", 
			"sp_MSdroparticletriggers" => "3", 
			"sp_MSdropconstraints" => "3", 
			"sp_MSdropmergepub_snaHFileot" => "3", 
			"sp_MSdropretry" => "3", 
			"sp_MSdummyupdate" => "3", 
			"sp_MSenum_replication_agents" => "3", 
			"sp_MSenum_replication_job" => "3", 
			"sp_MSenum3rdpartypublications" => "3", 
			"sp_MSenumallpublications" => "3", 
			"sp_MSenumchanges" => "3", 
			"sp_MSenumcolumns" => "3", 
			"sp_MSenumdeletesmetadata" => "3", 
			"sp_MSenumgenerations" => "3", 
			"sp_MSenummergepublications" => "3", 
			"sp_MSenumpartialchanges" => "3", 
			"sp_MSenumpartialdeletes" => "3", 
			"sp_MSenumpubreferences" => "3", 
			"sp_MSenumreplicas" => "3", 
			"sp_MSenumretries" => "3", 
			"sp_MSenumschemachange" => "3", 
			"sp_MSenumtranpublications" => "3", 
			"sp_MSexists_file" => "3", 
			"sp_MSexpandbelongs" => "3", 
			"sp_MSexpandnotbelongs" => "3", 
			"sp_MSexpandsubsnb" => "3", 
			"sp_MSfilterclause" => "3", 
			"sp_MSflush_access_cache" => "3", 
			"sp_MSflush_command" => "3", 
			"sp_MSforeach_worker" => "3", 
			"sp_MSforeachdb" => "3", 
			"sp_MSforeachtable" => "3", 
			"sp_MSgen_sync_tran_procs" => "3", 
			"sp_MSgenreplnickname" => "3", 
			"sp_MSgentablenickname" => "3", 
			"sp_MSget_col_position" => "3", 
			"sp_MSget_colinfo" => "3", 
			"sp_MSget_oledbinfo" => "3", 
			"sp_MSget_publisher_rpc" => "3", 
			"sp_MSget_qualifed_name" => "3", 
			"sp_MSget_synctran_commands" => "3", 
			"sp_MSget_type" => "3", 
			"sp_MSgetalertinfo" => "3", 
			"sp_MSgetchangecount" => "3", 
			"sp_MSgetconflictinsertproc" => "3", 
			"sp_MSgetlastrecgen" => "3", 
			"sp_MSgetlastsentgen" => "3", 
			"sp_MSgetonerow" => "3", 
			"sp_MSgetreplicainfo" => "3", 
			"sp_MSgetreplnick" => "3", 
			"sp_MSgetrowmetadata" => "3", 
			"sp_MSguidtostr" => "3", 
			"sp_MShelp_distdb" => "3", 
			"sp_MShelp_replication_status" => "3", 
			"sp_MShelpcolumns" => "3", 
			"sp_MShelpfulltextindex" => "3", 
			"sp_MShelpindex" => "3", 
			"sp_MShelpmergearticles" => "3", 
			"sp_MShelpobjectpublications" => "3", 
			"sp_MShelptype" => "3", 
			"sp_MSIfExistsRemoteLogin" => "3", 
			"sp_MSindexcolfrombin" => "3", 
			"sp_MSindexspace" => "3", 
			"sp_MSinit_replication_perfmon" => "3", 
			"sp_MSinsertcontents" => "3", 
			"sp_MSinsertdeleteconflict" => "3", 
			"sp_MSinsertgenhistory" => "3", 
			"sp_MSinsertschemachange" => "3", 
			"sp_MSis_col_replicated" => "3", 
			"sp_MSis_pk_col" => "3", 
			"sp_MSkilldb" => "3", 
			"sp_MSload_replication_status" => "3", 
			"sp_MSlocktable" => "3", 
			"sp_MSloginmappings" => "3", 
			"sp_MSmakearticleprocs" => "3", 
			"sp_MSmakeconflictinsertproc" => "3", 
			"sp_MSmakeexpandproc" => "3", 
			"sp_MSmakegeneration" => "3", 
			"sp_MSmakeinsertproc" => "3", 
			"sp_MSmakejoinfilter" => "3", 
			"sp_MSmakeselectproc" => "3", 
			"sp_MSmakesystableviews" => "3", 
			"sp_MSmaketempinsertproc" => "3", 
			"sp_MSmakeupdateproc" => "3", 
			"sp_MSmakeviewproc" => "3", 
			"sp_MSmaptype" => "3", 
			"sp_MSmark_proc_norepl" => "3", 
			"sp_MSmatchkey" => "3", 
			"sp_MSmergepublishdb" => "3", 
			"sp_MSmergesubscribedb" => "3", 
			"sp_MSobjectprivs" => "3", 
			"sp_MSpad_command" => "3", 
			"sp_MSproxiedmetadata" => "3", 
			"sp_MSpublicationcleanup" => "3", 
			"sp_MSpublicationview" => "3", 
			"sp_MSpublishdb" => "3", 
			"sp_MSrefcnt" => "3", 
			"sp_MSregistersubscription" => "3", 
			"sp_MSreinit_failed_subscriptions" => "3", 
			"sp_MSrepl_addrolemember" => "3", 
			"sp_MSrepl_dbrole" => "3", 
			"sp_MSrepl_droprolemember" => "3", 
			"sp_MSrepl_encrypt" => "3", 
			"sp_MSrepl_linkedservers_rowset" => "3", 
			"sp_MSrepl_startup" => "3", 
			"sp_MSreplcheck_connection" => "3", 
			"sp_MSreplcheck_publish" => "3", 
			"sp_MSreplcheck_pull" => "3", 
			"sp_MSreplcheck_subscribe" => "3", 
			"sp_MSreplicationcompatlevel" => "3", 
			"sp_MSreplrole" => "3", 
			"sp_MSreplsup_table_has_pk" => "3", 
			"sp_MSscript_beginproc" => "3", 
			"sp_MSscript_begintrig1" => "3", 
			"sp_MSscript_begintrig2" => "3", 
			"sp_MSscript_delete_statement" => "3", 
			"sp_MSscript_dri" => "3", 
			"sp_MSscript_endproc" => "3", 
			"sp_MSscript_endtrig" => "3", 
			"sp_MSscript_insert_statement" => "3", 
			"sp_MSscript_multirow_trigger" => "3", 
			"sp_MSscript_params" => "3", 
			"sp_MSscript_security" => "3", 
			"sp_MSscript_singlerow_trigger" => "3", 
			"sp_MSscript_sync_del_proc" => "3", 
			"sp_MSscript_sync_del_trig" => "3", 
			"sp_MSscript_sync_ins_proc" => "3", 
			"sp_MSscript_sync_ins_trig" => "3", 
			"sp_MSscript_sync_upd_proc" => "3", 
			"sp_MSscript_sync_upd_trig" => "3", 
			"sp_MSscript_trigger_assignment" => "3", 
			"sp_MSscript_trigger_exec_rpc" => "3", 
			"sp_MSscript_trigger_fetch_statement" => "3", 
			"sp_MSscript_trigger_update_checks" => "3", 
			"sp_MSscript_trigger_updates" => "3", 
			"sp_MSscript_trigger_variables" => "3", 
			"sp_MSscript_update_statement" => "3", 
			"sp_MSscript_where_clause" => "3", 
			"sp_MSscriptdatabase" => "3", 
			"sp_MSscriptdb_worker" => "3", 
			"sp_MSsetaccesslist" => "3", 
			"sp_MSsetalertinfo" => "3", 
			"sp_MSsetartprocs" => "3", 
			"sp_MSsetbit" => "3", 
			"sp_MSsetconflictscript" => "3", 
			"sp_MSsetconflicttable" => "3", 
			"sp_MSsetfilteredstatus" => "3", 
			"sp_MSsetfilterparent" => "3", 
			"sp_MSsetlastrecgen" => "3", 
			"sp_MSsetlastsentgen" => "3", 
			"sp_MSsetreplicainfo" => "3", 
			"sp_MSsetreplicastatus" => "3", 
			"sp_MSsetrowmetadata" => "3", 
			"sp_MSsettopology" => "3", 
			"sp_MSsetupbelongs" => "3", 
			"sp_MSSQLDMO70_version" => "3", 
			"sp_MSSQLOLE_version" => "3", 
			"sp_MSSQLOLE65_version" => "3", 
			"sp_MSsubscribedb" => "3", 
			"sp_MSsubscriptions" => "3", 
			"sp_MSsubscriptionvalidated" => "3", 
			"sp_MSsubsetpublication" => "3", 
			"sp_MStable_has_unique_index" => "3", 
			"sp_MStable_not_modifiable" => "3", 
			"sp_MStablechecks" => "3", 
			"sp_MStablekeys" => "3", 
			"sp_MStablenamefromnick" => "3", 
			"sp_MStablenickname" => "3", 
			"sp_MStablerefs" => "3", 
			"sp_MStablespace" => "3", 
			"sp_MStestbit" => "3", 
			"sp_MStextcolstatus" => "3", 
			"sp_MSunc_to_drive" => "3", 
			"sp_MSuniquecolname" => "3", 
			"sp_MSuniquename" => "3", 
			"sp_MSuniqueobjectname" => "3", 
			"sp_MSuniquetempname" => "3", 
			"sp_MSunmarkreplinfo" => "3", 
			"sp_MSunregistersubscription" => "3", 
			"sp_MSupdate_agenttype_default" => "3", 
			"sp_MSupdate_replication_status" => "3", 
			"sp_MSupdatecontents" => "3", 
			"sp_MSupdategenhistory" => "3", 
			"sp_MSupdateschemachange" => "3", 
			"sp_MSupdatesysmergearticles" => "3", 
			"sp_msupg_createcatalogcomputedcols" => "3", 
			"sp_msupg_dosystabcatalogupgrades" => "3", 
			"sp_msupg_dropcatalogcomputedcols" => "3", 
			"sp_msupg_recreatecatalogfaketables" => "3", 
			"sp_msupg_recreatesystemviews" => "3", 
			"sp_msupg_removesystemcomputedcolumns" => "3", 
			"sp_msupg_upgradecatalog" => "3", 
			"sp_MSuplineageversion" => "3", 
			"sp_MSvalidatearticle" => "3", 
			"sp_OACreate" => "3", 
			"sp_OADestroy" => "3", 
			"sp_OAGetErrorInfo" => "3", 
			"sp_OAGetProperty" => "3", 
			"sp_OAMethod" => "3", 
			"sp_OASetProperty" => "3", 
			"sp_OAStop" => "3", 
			"sp_objectfilegroup" => "3", 
			"sp_oledbinfo" => "3", 
			"sp_password" => "3", 
			"sp_pkeys" => "3", 
			"sp_prepare" => "3", 
			"sp_primary_keys_rowset" => "3", 
			"sp_primarykeys" => "3", 
			"sp_probe_xact" => "3", 
			"sp_procedure_params_rowset" => "3", 
			"sp_procedures_rowset" => "3", 
			"sp_processinfo" => "3", 
			"sp_processmail" => "3", 
			"sp_procoption" => "3", 
			"sp_provider_types_rowset" => "3", 
			"sp_publication_validation" => "3", 
			"sp_publishdb" => "3", 
			"sp_recompile" => "3", 
			"sp_refreshsubscriptions" => "3", 
			"sp_refreshview" => "3", 
			"sp_reinitmergepullsubscription" => "3", 
			"sp_reinitmergesubscription" => "3", 
			"sp_reinitpullsubscription" => "3", 
			"sp_reinitsubscription" => "3", 
			"sp_remoteoption" => "3", 
			"sp_remove_tempdb_file" => "3", 
			"sp_remove_xact" => "3", 
			"sp_removedbreplication" => "3", 
			"sp_removesrvreplication" => "3", 
			"sp_rename" => "3", 
			"sp_renamedb" => "3", 
			"sp_replcmds" => "3", 
			"sp_replcounters" => "3", 
			"sp_repldone" => "3", 
			"sp_replflush" => "3", 
			"sp_replica" => "3", 
			"sp_replication_agent_checkup" => "3", 
			"sp_replicationdboption" => "3", 
			"sp_replicationoption" => "3", 
			"sp_replincrementlsn" => "3", 
			"sp_replpostcmd" => "3", 
			"sp_replsetoriginator" => "3", 
			"sp_replshowcmds" => "3", 
			"sp_replsync" => "3", 
			"sp_repltrans" => "3", 
			"sp_replupdateschema" => "3", 
			"sp_reset_connection" => "3", 
			"sp_revoke_publication_access" => "3", 
			"sp_revokedbaccess" => "3", 
			"sp_revokelogin" => "3", 
			"sp_runwebtask" => "3", 
			"sp_scan_xact" => "3", 
			"sp_schemata_rowset" => "3", 
			"sp_script_synctran_commands" => "3", 
			"sp_scriptdelproc" => "3", 
			"sp_scriptinsproc" => "3", 
			"sp_scriptmappedupdproc" => "3", 
			"sp_scriptpkwhereclause" => "3", 
			"sp_scriptupdateparams" => "3", 
			"sp_scriptupdproc" => "3", 
			"sp_sdidebug" => "3", 
			"sp_sem_start_mail" => "3", 
			"sp_server_info" => "3", 
			"sp_serveroption" => "3", 
			"sp_setapprole" => "3", 
			"sp_setnetname" => "3", 
			"sp_spaceused" => "3", 
			"sp_special_columns" => "3", 
			"sp_sproc_columns" => "3", 
			"sp_sqlexec" => "3", 
			"sp_sqlregister" => "3", 
			"sp_srvrolepermission" => "3", 
			"sp_start_xact" => "3", 
			"sp_stat_xact" => "3", 
			"sp_statistics" => "3", 
			"sp_statistics_rowset" => "3", 
			"sp_stored_procedures" => "3", 
			"sp_subscribe" => "3", 
			"sp_subscription_cleanup" => "3", 
			"sp_subscriptioncleanup" => "3", 
			"sp_table_privileges" => "3", 
			"sp_table_privileges_ex" => "3", 
			"sp_table_privileges_rowset" => "3", 
			"sp_table_validation" => "3", 
			"sp_tableoption" => "3", 
			"sp_tables" => "3", 
			"sp_tables_ex" => "3", 
			"sp_tables_info_rowset" => "3", 
			"sp_tables_rowset" => "3", 
			"sp_tempdbspace" => "3", 
			"sp_unbindefault" => "3", 
			"sp_unbindrule" => "3", 
			"sp_unmakestartup" => "3", 
			"sp_unprepare" => "3", 
			"sp_unsubscribe" => "3", 
			"sp_updatestats" => "3", 
			"sp_user_counter1" => "3", 
			"sp_user_counter10" => "3", 
			"sp_user_counter2" => "3", 
			"sp_user_counter3" => "3", 
			"sp_user_counter4" => "3", 
			"sp_user_counter5" => "3", 
			"sp_user_counter6" => "3", 
			"sp_user_counter7" => "3", 
			"sp_user_counter8" => "3", 
			"sp_user_counter9" => "3", 
			"sp_validatelogins" => "3", 
			"sp_validlang" => "3", 
			"sp_validname" => "3", 
			"sp_who" => "3", 
			"sp_who2" => "3", 
			"spt_committab" => "3", 
			"spt_datatype_info" => "3", 
			"spt_datatype_info_ext" => "3", 
			"spt_fallback_db" => "3", 
			"spt_fallback_dev" => "3", 
			"spt_fallback_usg" => "3", 
			"spt_monitor" => "3", 
			"spt_provider_types" => "3", 
			"spt_server_info" => "3", 
			"spt_values" => "3", 
			"sysallocations" => "3", 
			"sysalternates" => "3", 
			"sysaltfiles" => "3", 
			"syscacheobjects" => "3", 
			"syscharsets" => "3", 
			"syscolumns" => "3", 
			"syscomments" => "3", 
			"sysconfigures" => "3", 
			"sysconstraints" => "3", 
			"syscurconfigs" => "3", 
			"syscursorcolumns" => "3", 
			"syscursorrefs" => "3", 
			"syscursors" => "3", 
			"syscursortables" => "3", 
			"sysdatabases" => "3", 
			"sysdepends" => "3", 
			"sysdevices" => "3", 
			"sysfilegroups" => "3", 
			"sysfiles" => "3", 
			"sysfiles1" => "3", 
			"sysforeignkeys" => "3", 
			"sysfulltextcatalogs" => "3", 
			"sysindexes" => "3", 
			"sysindexkeys" => "3", 
			"syslanguages" => "3", 
			"syslockinfo" => "3", 
			"syslocks" => "3", 
			"syslogins" => "3", 
			"sysmembers" => "3", 
			"sysmessages" => "3", 
			"sysobjects" => "3", 
			"sysoledbusers" => "3", 
			"sysperfinfo" => "3", 
			"syspermissions" => "3", 
			"sysprocesses" => "3", 
			"sysprotects" => "3", 
			"sysreferences" => "3", 
			"SYSREMOTE_CATALOGS" => "3", 
			"SYSREMOTE_COLUMN_PRIVILEGES" => "3", 
			"SYSREMOTE_COLUMNS" => "3", 
			"SYSREMOTE_FOREIGN_KEYS" => "3", 
			"SYSREMOTE_INDEXES" => "3", 
			"SYSREMOTE_PRIMARY_KEYS" => "3", 
			"SYSREMOTE_PROVIDER_TYPES" => "3", 
			"SYSREMOTE_SCHEMATA" => "3", 
			"SYSREMOTE_STATISTICS" => "3", 
			"SYSREMOTE_TABLE_PRIVILEGES" => "3", 
			"SYSREMOTE_TABLES" => "3", 
			"SYSREMOTE_VIEWS" => "3", 
			"sysremotelogins" => "3", 
			"syssegments" => "3", 
			"sysservers" => "3", 
			"systypes" => "3", 
			"sysusers" => "3", 
			"sysxlogins" => "3", 
			"xp_availablemedia" => "3", 
			"xp_check_query_results" => "3", 
			"xp_cleanupwebtask" => "3", 
			"xp_cmdshell" => "3", 
			"xp_deletemail" => "3", 
			"xp_dirtree" => "3", 
			"xp_displayparamstmt" => "3", 
			"xp_dropwebtask" => "3", 
			"xp_dsninfo" => "3", 
			"xp_enum_activescriptengines" => "3", 
			"xp_enum_oledb_providers" => "3", 
			"xp_enumcodepages" => "3", 
			"xp_enumdsn" => "3", 
			"xp_enumerrorlogs" => "3", 
			"xp_enumgroups" => "3", 
			"xp_eventlog" => "3", 
			"xp_execresultset" => "3", 
			"xp_fileexist" => "3", 
			"xp_findnextmsg" => "3", 
			"xp_fixeddrives" => "3", 
			"xp_get_mapi_default_profile" => "3", 
			"xp_get_mapi_profiles" => "3", 
			"xp_get_tape_devices" => "3", 
			"xp_getfiledetails" => "3", 
			"xp_getnetname" => "3", 
			"xp_grantlogin" => "3", 
			"xp_initcolvs" => "3", 
			"xp_intersectbitmaps" => "3", 
			"xp_load_dummy_handlers" => "3", 
			"xp_logevent" => "3", 
			"xp_loginconfig" => "3", 
			"xp_logininfo" => "3", 
			"xp_makewebtask" => "3", 
			"xp_mergexpusage" => "3", 
			"xp_msver" => "3", 
			"xp_msx_enlist" => "3", 
			"xp_ntsec_enumdomains" => "3", 
			"xp_ntsec_enumgroups" => "3", 
			"xp_ntsec_enumusers" => "3", 
			"xp_oledbinfo" => "3", 
			"xp_param_dump" => "3", 
			"xp_perfend" => "3", 
			"xp_perfmonitor" => "3", 
			"xp_perfsample" => "3", 
			"xp_perfstart" => "3", 
			"xp_printstatements" => "3", 
			"xp_proxiedmetadata" => "3", 
			"xp_qv" => "3", 
			"xp_readerrorlog" => "3", 
			"xp_readmail" => "3", 
			"xp_regaddmultistring" => "3", 
			"xp_regdeletekey" => "3", 
			"xp_regdeletevalue" => "3", 
			"xp_regenumvalues" => "3", 
			"xp_regread" => "3", 
			"xp_regremovemultistring" => "3", 
			"xp_regwrite" => "3", 
			"xp_revokelogin" => "3", 
			"xp_runwebtask" => "3", 
			"xp_sendmail" => "3", 
			"xp_servicecontrol" => "3", 
			"xp_showcolv" => "3", 
			"xp_showlineage" => "3", 
			"xp_snmp_getstate" => "3", 
			"xp_snmp_raisetrap" => "3", 
			"xp_sprintf" => "3", 
			"xp_sqlagent_enum_jobs" => "3", 
			"xp_sqlagent_is_starting" => "3", 
			"xp_sqlagent_monitor" => "3", 
			"xp_sqlagent_notify" => "3", 
			"xp_sqlinventory" => "3", 
			"xp_sqlmaint" => "3", 
			"xp_sqlregister" => "3", 
			"xp_sqltrace" => "3", 
			"xp_sscanf" => "3", 
			"xp_startmail" => "3", 
			"xp_stopmail" => "3", 
			"xp_subdirs" => "3", 
			"xp_terminate_process" => "3", 
			"xp_test_mapi_profile" => "3", 
			"xp_trace_addnewqueue" => "3", 
			"xp_trace_deletequeuedefinition" => "3", 
			"xp_trace_destroyqueue" => "3", 
			"xp_trace_enumqueuedefname" => "3", 
			"xp_trace_enumqueuehandles" => "3", 
			"xp_trace_eventclassrequired" => "3", 
			"xp_trace_flushqueryhistory" => "3", 
			"xp_trace_generate_event" => "3", 
			"xp_trace_getappfilter" => "3", 
			"xp_trace_getconnectionidfilter" => "3", 
			"xp_trace_getcpufilter" => "3", 
			"xp_trace_getdbidfilter" => "3", 
			"xp_trace_getdurationfilter" => "3", 
			"xp_trace_geteventfilter" => "3", 
			"xp_trace_geteventnames" => "3", 
			"xp_trace_getevents" => "3", 
			"xp_trace_gethostfilter" => "3", 
			"xp_trace_gethpidfilter" => "3", 
			"xp_trace_getindidfilter" => "3", 
			"xp_trace_getntdmfilter" => "3", 
			"xp_trace_getntnmfilter" => "3", 
			"xp_trace_getobjidfilter" => "3", 
			"xp_trace_getqueueautostart" => "3", 
			"xp_trace_getqueuecreateinfo" => "3", 
			"xp_trace_getqueuedestination" => "3", 
			"xp_trace_getqueueproperties" => "3", 
			"xp_trace_getreadfilter" => "3", 
			"xp_trace_getserverfilter" => "3", 
			"xp_trace_getseverityfilter" => "3", 
			"xp_trace_getspidfilter" => "3", 
			"xp_trace_gettextfilter" => "3", 
			"xp_trace_getuserfilter" => "3", 
			"xp_trace_getwritefilter" => "3", 
			"xp_trace_loadqueuedefinition" => "3", 
			"xp_trace_opentracefile" => "3", 
			"xp_trace_pausequeue" => "3", 
			"xp_trace_restartqueue" => "3", 
			"xp_trace_savequeuedefinition" => "3", 
			"xp_trace_setappfilter" => "3", 
			"xp_trace_setconnectionidfilter" => "3", 
			"xp_trace_setcpufilter" => "3", 
			"xp_trace_setdbidfilter" => "3", 
			"xp_trace_setdurationfilter" => "3", 
			"xp_trace_seteventclassrequired" => "3", 
			"xp_trace_seteventfilter" => "3", 
			"xp_trace_sethostfilter" => "3", 
			"xp_trace_sethpidfilter" => "3", 
			"xp_trace_setindidfilter" => "3", 
			"xp_trace_setntdmfilter" => "3", 
			"xp_trace_setntnmfilter" => "3", 
			"xp_trace_setobjidfilter" => "3", 
			"xp_trace_setqueryhistory" => "3", 
			"xp_trace_setqueueautostart" => "3", 
			"xp_trace_setqueuecreateinfo" => "3", 
			"xp_trace_setqueuedestination" => "3", 
			"xp_trace_setreadfilter" => "3", 
			"xp_trace_setserverfilter" => "3", 
			"xp_trace_setseverityfilter" => "3", 
			"xp_trace_setspidfilter" => "3", 
			"xp_trace_settextfilter" => "3", 
			"xp_trace_setuserfilter" => "3", 
			"xp_trace_setwritefilter" => "3", 
			"xp_trace_startconsumer" => "3", 
			"xp_trace_toeventlogconsumer" => "3", 
			"xp_trace_tofileconsumer" => "3", 
			"xp_unc_to_drive" => "3", 
			"xp_unload_dummy_handlers" => "3", 
			"xp_updatecolvbm" => "3", 
			"xp_updatelineage" => "3", 
			"xp_varbintohexstr" => "3", 
			"xp_writesqlinfo" => "3", 
			"+" => "4", 
			"-" => "4", 
			"=" => "4", 
			"//" => "4", 
			"/" => "4", 
			"%" => "4", 
			">" => "4", 
			"<" => "4", 
			"!" => "4", 
			"|" => "4", 
			"[" => "4", 
			"]" => "4", 
			"(" => "4", 
			")" => "4", 
			"!=" => "4", 
			"<=" => "4", 
			">=" => "4", 
			"LIKE" => "4");

// Special extensions

// Each category can specify a PHP function that returns an altered
// version of the keyword.
        
        

$this->linkscripts    	= array(
			"1" => "donothing", 
			"2" => "donothing", 
			"3" => "donothing", 
			"4" => "donothing");
}


function donothing($keywordin)
{
	return $keywordin;
}

}?>
