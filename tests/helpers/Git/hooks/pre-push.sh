#!/bin/sh

set -o nounset
set -o errexit

if [ "${CCM_SKIP_CHECK_TAGPUSH:-}" = '1' ]; then
    echo 'Skipping check pushing tags (CCM_SKIP_CHECK_TAGPUSH environment variable is set to 1)'
    exit 0
fi

ZERO_SHA='0000000000000000000000000000000000000000'
rc=0

while read -r localRef localSha remoteRef remoteSha; do
    case "$remoteRef" in
        refs/tags/*)
            ;;
        *)
            # Not pushing a tag
            continue
        ;;
    esac
    if [ "$localSha" = "$ZERO_SHA" ]; then
        # Deleting a remote tag
        continue
    fi
    tag="${remoteRef#refs/tags/}"
    if ! printf '%s\n' "$tag" | grep -Eq '^[0-9]+(\.[0-9]+){1,3}([.-]?[a-zA-Z]+[0-9]+)?$'; then
        # Tag is not a version tag: skipping checks
        printf 'Tag %s is not a version tag: skipping checks\n' "$tag"
        continue
    fi
    echo "Checking tag $tag (set CCM_SKIP_CHECK_TAGPUSH=1 to skip)"
    tagOk=1
    configVersion="$(git show "$localSha:concrete/config/concrete.php" | sed -n "s/^[[:space:]]*[\"']version[\"'][[:space:]]*=>[[:space:]]*[\"']\([^'\"]*\)[\"'],.*/\1/p" | head -n 1)"
    if [ "$configVersion" != "$tag" ]; then
        printf "ERROR: you are going to push tag '%s' but concrete/config/concrete.php has version '%s'\n" "$tag" "$configVersion" >&2
        tagOk=0
    fi
    zipVersion="$(git show "$localSha:build/tasks/build-release/download.js" | sed -n "s#.*archive/refs/tags/\([^/]*\)\.zip'.*#\1#p" | head -n 1)"
    if [ "$zipVersion" != "$tag" ]; then
        printf "ERROR: you are going to push tag '%s' but build/tasks/build-release/download.js points to '%s'\n" "$tag" "$zipVersion" >&2
        tagOk=0
    fi
    if [ $tagOk -eq 1 ]; then
        echo "Tag $tag matches the version in the source files"
    else
        rc=1
    fi
done

exit $rc
