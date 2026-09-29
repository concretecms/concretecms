# Instructions for AI tools

These rules apply to any text you write for this repository: pull request descriptions, review comments, issue reports, commit messages.
Maintainers read everything by hand: reading your text must take less time than reading the diff.

[CONTRIBUTING.md](CONTRIBUTING.md) applies to you too: read it for the AI policy and the coding style rules.

## Pull request descriptions

- At most ~10 lines, with this structure: what changes, why, how to test it.
- Link the issue being fixed (`Fix #123`) instead of describing it again.
- The "why" must come from the human author: if you don't know it, ask for it, don't invent it.
- Do not summarize the diff file by file, and do not repeat what the code already says.
- No lists of benefits, no "Summary"/"Overview"/"Conclusion" sections, no emoji, no praise.
- Mention only what a reviewer can't see from the diff: side effects, backward compatibility breaks, things deliberately left out.

## Comments on pull requests and issues

- One comment per problem, only if it's actionable: say what's wrong and what to do instead.
- No compliments, no paraphrases of the code, no recap of the previous comments.
- When answering a question, answer it in the first sentence.

## Commit messages

- A subject line of at most ~70 characters; add a body only when the reason is not obvious.
