# Targets without dependencies that should be run unconditionally.
.PHONY: check

# This could be set to /usr for a genuine system installation.
PREFIX := /usr/local

# The package name.
PN := XML-Schema-learner

# SRCS is a list of all source files.
SRCS := $(shell find src/ -type f)

# ... and the executable.
EXE  := schema-learn
SRCS := $(EXE) $(SRCS)

# DSTS is a list of all files that should be installed (with full
# paths).
DSTS := $(addprefix $(DESTDIR)$(PREFIX)/share/$(PN)/, $(SRCS))

# Run the test suite.
check:
	phpunit tests/suite.php


# Install all SRCS to their DSTS counterpart, and create a symlink for
# the executable script.
install: $(DSTS) $(DESTDIR)$(PREFIX)/bin/$(EXE)


# This pattern match makes each item in DSTS depend on its
# corresponding entry in SRCS.
$(DESTDIR)$(PREFIX)/share/$(PN)/%: %
	install -D $< $@


# Likewise, this makes the symlink creation dependend upon the
# executable. We create the target directory first if it does not
# exist.
$(DESTDIR)$(PREFIX)/bin/$(EXE): $(EXE)
	install -d $(DESTDIR)$(PREFIX)/bin
	ln -sf $(DESTDIR)$(PREFIX)/share/$(PN)/$(EXE) \
	       $(DESTDIR)$(PREFIX)/bin/$(EXE)
