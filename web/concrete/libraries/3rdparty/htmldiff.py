#!/usr/bin/python
"""HTML Diff: http://www.aaronsw.com/2002/diff
Rough code, badly documented. Send me comments and patches."""

__author__ = 'Aaron Swartz <me@aaronsw.com>'
__copyright__ = '(C) 2003 Aaron Swartz. GNU GPL 2. Licensed by concrete5 under the LGPL by special agreement with the original copyright holder.' 
__version__ = '0.22'

import difflib, string

def isTag(x): return x[0] == "<" and x[-1] == ">"

def textDiff(a, b):
	"""Takes in strings a and b and returns a human-readable HTML diff."""

	out = []
	a, b = html2list(a), html2list(b)
	s = difflib.SequenceMatcher(None, a, b)
	for e in s.get_opcodes():
		if e[0] == "replace":
			# @@ need to do something more complicated here
			# call textDiff but not for html, but for some html... ugh
			# gonna cop-out for now
			out.append('<del class="diff modified">'+''.join(a[e[1]:e[2]]) + '</del><ins class="diff modified">'+''.join(b[e[3]:e[4]])+"</ins>")
		elif e[0] == "delete":
			out.append('<del class="diff">'+ ''.join(a[e[1]:e[2]]) + "</del>")
		elif e[0] == "insert":
			out.append('<ins class="diff">'+''.join(b[e[3]:e[4]]) + "</ins>")
		elif e[0] == "equal":
			out.append(''.join(b[e[3]:e[4]]))
		else: 
			raise "Um, something's broken. I didn't expect a '" + `e[0]` + "'."
	return ''.join(out)

def html2list(x, b=0):
	mode = 'char'
	cur = ''
	out = []
	for c in x:
		if mode == 'tag':
			if c == '>': 
				if b: cur += ']'
				else: cur += c
				out.append(cur); cur = ''; mode = 'char'
			else: cur += c
		elif mode == 'char':
			if c == '<': 
				out.append(cur)
				if b: cur = '['
				else: cur = c
				mode = 'tag'
			elif c in string.whitespace: out.append(cur+c); cur = ''
			else: cur += c
	out.append(cur)
	return filter(lambda x: x is not '', out)

if __name__ == '__main__':
	import sys
	try:
		a, b = sys.argv[1:3]
	except ValueError:
		print "htmldiff: highlight the differences between two html files"
		print "usage: " + sys.argv[0] + " a b"
		sys.exit(1)
	print textDiff(open(a).read(), open(b).read())
	
