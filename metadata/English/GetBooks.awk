# Scott Starker
# Bible books and partial books
# 5/27/17

# gawk -f GetBooks.awk ISO_Language.xml

BEGIN {
	nt_PDF = 0;
	nt_partial_PDF = 0;
	ot_PDF = 0;
	ot_partial_PDF = 0;
	#languages = 0;			# Won't work because ISO_Language.xml doesn't have ALL of the languages.
}

{
	if ($0 ~ /\<content/) {
		#languages++;
		nt = 0;
		nt_p = 0;
		ot = 0;
		ot_p = 0;
		nt = strtonum(gensub(/.*nt_PDF=\"([0-9]+)\".*/, "\\1", 1, $0));
# print gensub(/.*nt_PDF=\"([0-9]+)\".*/, "\\1", 1, $0);
		nt_p = strtonum(gensub(/.*nt_partial_PDF=\"([0-9]+)\".*/, "\\1", 1, $0));
		ot = strtonum(gensub(/.*ot_PDF=\"([0-9]+)\".*/, "\\1", 1, $0));
		ot_p = strtonum(gensub(/.*ot_partial_PDF=\"([0-9]+)\".*/, "\\1", 1, $0));
		if (nt > 0) {
			nt_PDF++;
		}
		if (nt_p > 0 && nt_p < 27) {	# one less
# print gensub(/.*nt_partial_PDF=\"([0-9]+)\".*/, "\\1", 1, $0);
			nt_partial_PDF++;
		}
		if (ot > 0) {
			ot_PDF++;
		}
		if (ot_p > 0 && ot_p < 39) {	# one less
# print gensub(/.*ot_partial_PDF=\"([0-9]+)\".*/, "\\1", 1, $0);
			ot_partial_PDF++;
		}
	}
}

END {
	print "NT PDFs: " nt_PDF "	NT partial PDFs: " nt_partial_PDF "	OT PDF: " ot_PDF "	OT partial PDFs: " ot_partial_PDF;
}