const { GoogleGenerativeAI } = require("@google/generative-ai");
const github = require("@actions/github");
const { execSync } = require("child_process");

async function run() {
  try {
    const token = process.env.GITHUB_TOKEN;
    const apiKey = process.env.GEMINI_API_KEY;
    if (!apiKey) {
      console.error("GEMINI_API_KEY is not set.");
      process.exit(1);
    }

    const octokit = github.getOctokit(token);
    const context = github.context;
    
    if (!context.payload.pull_request) {
      console.log("Not a pull request. Skipping.");
      return;
    }

    const prNumber = context.payload.pull_request.number;

    // Fetch base branch to compute diff
    const baseRef = context.payload.pull_request.base.ref;
    execSync(`git fetch origin ${baseRef}`);
    const diff = execSync(`git diff origin/${baseRef}...HEAD`).toString();

    if (!diff.trim()) {
      console.log("No diff found. Skipping review.");
      return;
    }

    // Initialize Gemini API
    const genAI = new GoogleGenerativeAI(apiKey);
    const model = genAI.getGenerativeModel({ model: "gemini-2.5-flash" });

    const prompt = `You are an expert senior software engineer and code reviewer.
Review the following git diff of a Pull Request. Focus on:
1. Logic errors, bugs, or missing edge cases.
2. Security issues or vulnerabilities.
3. Performance optimization suggestions.
4. Clean code practices and readability.

Keep the review constructive, concise, and structured in Markdown format. If everything looks perfect, provide a friendly summary.

Here is the Git Diff:
\`\`\`diff
${diff}
\`\`\``;

    const result = await model.generateContent(prompt);
    const reviewMessage = result.response.text();

    const commentBody = `### 🤖 Gemini AI Code Reviewer Feedback

${reviewMessage}

---
*Powered by Google Gemini 2.5 Flash*`;

    // Post comment to PR
    await octokit.rest.issues.createComment({
      owner: context.repo.owner,
      repo: context.repo.repo,
      issue_number: prNumber,
      body: commentBody
    });

    console.log("Review comment posted successfully!");
  } catch (error) {
    console.error("Error running AI review:", error);
    process.exit(1);
  }
}

run();
