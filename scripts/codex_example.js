import OpenAI from 'openai';

const client = new OpenAI({ apiKey: process.env.OPENAI_API_KEY });

async function main() {
  try {
    const prompt = `Write a short JavaScript function that returns the sum of numbers in an array.`;

    const response = await client.responses.create({
      model: 'gpt-4o-mini',
      input: prompt,
      // new param name for the responses API
      max_output_tokens: 300
    });

    console.log('=== Prompt ===');
    console.log(prompt);
    console.log('\n=== Model Output ===');

    // Prefer response.output_text when available (convenience field)
    if (response.output_text) {
      console.log(response.output_text);
    } else if (response.output && response.output.length) {
      for (const item of response.output) {
        if (typeof item === 'string') console.log(item);
        else if (item.content && item.content.length) {
          for (const c of item.content) console.log(c.text || JSON.stringify(c));
        } else console.log(JSON.stringify(item));
      }
    } else {
      console.log(JSON.stringify(response, null, 2));
    }
  } catch (err) {
    console.error('Error calling OpenAI:', err.message || err);
  }
}

main();
