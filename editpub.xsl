<?xml version="1.0" encoding="utf-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
  <xsl:output method="xml" 
              doctype-system="http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd" 
              doctype-public="-//W3C//DTD XHTML 1.0 Transitional//EN" indent="yes" />

  <xsl:strip-space elements="*" />

  <xsl:param name="favicon">http://www.alari.ch/favicon.ico</xsl:param>

  <xsl:variable name="abstract">
    <xsl:choose>
      <xsl:when test="boolean(document(concat('abstracts/',$pubid,'-abstract.xml')))">
        <xsl:value-of select="document(concat('abstracts/',$pubid,'-abstract.xml'))//abstract" />
      </xsl:when>
      <xsl:otherwise>
        <xsl:value-of select="''"/>
      </xsl:otherwise>
    </xsl:choose>
  </xsl:variable>

  <xsl:template match="/">
    <html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en-us" lang="en-us" dir="ltr">
      <head>
        <title>Edit Publication</title>
        <link href="site.css" rel="stylesheet" type="text/css" />
        <script src="addpub.js" type="text/javascript"></script>
        <link rel="shortcut icon" href="{$favicon}"/>
      </head>
      <body>
        <div class="bebop">

          <xsl:choose>
            <xsl:when test="count(entries/entry[@name=$pubid])=1" >
              <xsl:for-each select="entries/entry[@name=$pubid]" >
                <xsl:call-template name="printForm" />
              </xsl:for-each>
            </xsl:when>
            <xsl:when test="count(entries/entry[@name=$pubid])>1" >
              <b>
                Error: The BibTeX ID <xsl:value-of select="$pubid" /> is not unique.
              </b>
            </xsl:when>
            <xsl:otherwise>
              <b>
                Error: No BibTeX-Entry with ID <xsl:value-of select="$pubid" />
                found!
              </b>
            </xsl:otherwise>
          </xsl:choose>
          <p>
            <a href="javascript:history.back()">... return</a>
          </p>
        </div>
      </body>
    </html>
  </xsl:template>

  <xsl:template name="printForm">
    <form name="bibentryform" id="bibentryform" action="">
      <xsl:call-template name="printBibTeXFields" />
      <xsl:call-template name="printExtendedBibTeXFields" />
    </form>

    <p>
      <button onclick="genBib()">Generate BibTeX code</button>
      Then preview it below and click the 'Publish' button.
    </p>

    <form name="bibcodeform" id="bibcodeform" method="post">
      <xsl:attribute name="action">
        <xsl:value-of select="concat('editpub.php?pub=', $pubid)" />
      </xsl:attribute>
      <label for="bibtexcode">BibTeX of your entry</label>
      <br />
      <textarea name="bibtexcode" id="bibtexcode" rows="10" cols="80"></textarea>
      <br />
      <br />
      <div>
        <label for="submit"></label>
        <input type="submit" name="submit" id="submit" value="Publish" />
      </div>
    </form>
    <script type="text/javascript">
          showExtendedFields();
          genBib();
        </script>
  </xsl:template>

  <xsl:template name="printBibTeXFields">
    <fieldset>
      <legend>Edit your publication</legend>
      <div>
        <label for="entrytype">Select publication type:</label>
        <br/>
        <select name="entrytype" id="entrytype" onChange="showForm(this.value)">
          <option value="article">Article (Journal paper)</option>
          <option value="book">Book</option>
          <option value="inproceedings">Inproceedings (Conference paper)</option>
          <option value="incollection">Incollection (Book chapter)</option>
          <option value="inbook">Inbook (Book chapter without titled chapter)</option>
          <option value="mastersthesis">Masters thesis</option>
          <option value="masproject">MAS project</option>
          <option value="phdthesis">PhD thesis</option>
          <option value="techreport">Technical report</option>
          <option value="misc">Misc (Patent, Presentation etc.)</option>
          <option value="unpublished">Unpublished</option>
        </select>
        <br/>
        <br/>
        <label for="entryname">entry ID(*)</label>
        <br/>
        <input type="text" name="entryname" id="entryname" value="{$pubid}" />
        <br/>
        <br/>
        <div id="entryform">
          <script type="text/javascript">
            <xsl:apply-templates select="child::node()" />
                val['abstract'] = '<xsl:value-of select="$abstract"/>';
                selectEntrtype('<xsl:value-of select="entrytype"/>');
                showForm('<xsl:value-of select="entrytype"/>');
              </script>
        </div>
      </div>
    </fieldset>
  </xsl:template>

  <!-- skip entrytype -->
  <xsl:template match="entry/entrytype" />
  <xsl:template match="entries"/>

  <xsl:template match="entry/*" >
                val['<xsl:value-of select="name(.)"/>']='<xsl:apply-templates />';
  </xsl:template>

  <xsl:template match="entry/authors">
                val['author']='<xsl:apply-templates />';
  </xsl:template>

  <xsl:template match="entry/authors/author">
    <xsl:value-of select="."/>
    <xsl:if test="following-sibling::node()">
      <xsl:text> and </xsl:text>
    </xsl:if>
  </xsl:template>

  <xsl:template match="keywords/keyword">
    <xsl:value-of select="."/>
    <xsl:if test="following-sibling::node()">
      <xsl:text>, </xsl:text>
    </xsl:if>
  </xsl:template>

  <xsl:template match="text()">
    <xsl:call-template name="escape-apos">
      <xsl:with-param name="string">
        <xsl:value-of select="." />
      </xsl:with-param>
    </xsl:call-template>
  </xsl:template>


  <xsl:template name="printExtendedBibTeXFields">
    <fieldset>
      <legend>Extended BibTeX fields</legend>
      <div>
        <label for="abstract">Abstract</label><br />
        <textarea name="abstract" id="abstract" rows="10" cols="80" />
        <br />
        <br />
        <label for="researcharea">Research area</label>
        <br />
        <input type="text" name="researcharea" id="researcharea" value="" />
        <br />
        <br />
        <label for="keywords">Keywords seperated with comma(,).</label>
        <br />
        <input type="text" name="keywords" id="keywords" value="" />
        <br />
        <br />
        <label for="filelink">File link</label>
        (Enter the URL to the full text document. If missing,
        <a href="javascript:openUploadPubForm()">click to upload the full text document</a>)
        <br />
        <input type="text" name="filelink" id="filelink" value="" />
        <br />
      </div>
    </fieldset>
  </xsl:template>

  <xsl:template name="escape-apos">
    <xsl:param name="string" />
    <!-- create an $apos variable to make it easier to refer to -->
    <xsl:variable name="apos" select='"&apos;"' />
    <xsl:choose>
      <!-- if the string contains an apostrophe... -->
      <xsl:when test='contains($string, $apos)' >
        <!-- ... give the value before the apostrophe... -->
        <xsl:value-of select="substring-before($string, $apos)" />
        <!-- ... the escaped apostrophe ... -->
        <xsl:text>\'</xsl:text>
        <!-- ... and the result of applying the template to the string
             after the apostrophe -->
        <xsl:call-template name="escape-apos">
          <xsl:with-param name="string"
                          select="substring-after($string, $apos)" />
        </xsl:call-template>
      </xsl:when>
      <xsl:otherwise>
        <!-- ... just give the value of the string -->
        <xsl:value-of select="$string" />
      </xsl:otherwise>
    </xsl:choose>
  </xsl:template>


</xsl:stylesheet>
