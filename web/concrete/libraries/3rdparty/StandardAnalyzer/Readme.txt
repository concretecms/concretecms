Firstly, the StandardAnalyzer License which covers the StandardAnalyzer code, except that
code which is already covered under another license. StandardAnalyzer is a modification
of the Zend Framework's code (see Zend_License.txt). All modifications, except for the 
English Stemmer, is Copyright (c) 2008, Kenneth Katzgrau. The official license is below.
The porterStemmer class is Copyright (c) 2005 Richard Heyes (http://www.phpguru.org/).
Special thanks to Richard for his algorithm.

---
Copyright (c) 2008, Kenneth Katzgrau <katzgrau@gmail.com>, CodeFury.Net (katzgrau.simplesample.org)
All rights reserved.

Redistribution and use in source and binary forms, with or without modification, are permitted provided that the following conditions are met:

    * Redistributions of source code must retain the above copyright notice, this list of conditions and the following disclaimer.
    * Redistributions in binary form must reproduce the above copyright notice, this list of conditions and the following disclaimer in the documentation and/or other materials provided with the distribution.
    * Neither the name of the Kenneth Katzgrau, CodeFury.Net nor the names of its contributors may be used to endorse or promote products derived from this software without specific prior written permission.

THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
---

How to use this analyzer:

StandardAnalyzer was created to work alongside your existing Zend installation.
For most users, their Zend installation is at the root of their web site.
The Unzip/place the StandardAnalyzer folder in the same directory as the Zend
folder.

StandardAnalyzer is used at two different times: Indexing documents, and
querying the index. For StandardAnalyzer to be used, you will need to do one of two things:

	1. 	a.	Find Analyzer.php in Zend/Search/Lucene/Analysis. Open it and add
				require_once 'StandardAnalyzer/Analyzer/Standard.php'
			to your code.
		b.	Find the function named 'getDefault'. You will want to change
			the conditional statement to say:
				self::$_defaultImpl = new StandardAnalyzer_Analyzer_Standard_English();
		c.	Now your Zend Lucene installation will always use StandardAnalyzer by default.
	
	2.	a.	If you don't feel like setting the default analyzer to be StandardAnalyzer,
			you will have to call:
				Zend_Search_Lucene_Analysis_Analyzer::setDefault(new StandardAnalyzer_Analyzer_Standard_English());
			before EVERY time you work with the index. Remember, indexing and searching documents
			require the same analyzer to work.

The first option because is a one-time change. Then again, it may not be the best
solution depending on your needs.

Answer to the question: Why is the StandardAnalyzer folder next to the Zend Folder,
and not inside?

Answer: I didn't want to create a pseudo-integration of StandardAnalyzer into the Zend Framework.
I wanted the StandardAnalyzer configuration and installation to be very simple, as well.
Putting the StandardAnalyzer files in their disgnated spots in a Zend installation might
create unneeded confusion (with all the files involved). If StandardAnalyzer is ever incorporated
into the Zend Framework, hopefully the good folks at Zend do that.

If developers want to integrate it anyway (as I've also done), you will have to do some
namespace and path changing to stick to the Zend convention (which I am a pretty big fan of).

Question? Comments?
	katzgrau@gmail.com
	codefury.net (or katzgrau.simplesample.org)