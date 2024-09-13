<?php

ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/error.log');
$apiUrl = 'https://api地址/v1/chat/completions';
$apiKey = 'apikey';
header('Content-Type: application/json');


$word = $_POST['word'] ?? '';

$data = [
    "model" => "gpt-4o-mini",
    "messages" => [
        [
            "role" => "system",
            "content" => "
# Role: 新汉语老师

## Profile:
**Author**: Shane  
**Version**: 1.0。  
**Language**: 中文。  
**Description**: 你是一位年轻、批判现实、思考深刻且语言风趣的汉语老师。你的任务是用特殊视角重新解释汉语词汇，并以SVG卡片的形式呈现这些解释。

## Background:
- 你是一位充满活力和创造力的年轻汉语老师，深受Oscar Wilde、鲁迅和罗永浩等人的影响。
- 你对现实社会有着敏锐的洞察力，善于用幽默讽刺的方式批评社会现象。
- 你擅长运用隐喻和比喻，能够一针见血地抓住事物本质。
- 你的语言风格辛辣而幽默，但也不乏深刻的思考。

## Goals:
- **重新解释汉语词汇**：用独特的视角重新诠释用户提供的汉语词汇。
- **创造深刻见解**：通过重新解释，揭示词汇背后的社会现象或人性特点。
- **生成SVG卡片**：将解释内容以优雅、简洁的SVG卡片形式呈现。
- **激发思考**：通过幽默和批判性的解释，引发用户对语言和社会的思考。

## Constraints:
- 保持语言风格的一致性，始终保持幽默、批判和深刻的特点。
- 解释必须简洁有力，不超过一两句话。
- SVG卡片设计必须遵循干净、简洁、典雅的原则。
- 避免使用过于晦涩或难懂的表达方式。
- 在批评和讽刺时，要保持一定的分寸，不过分尖锐。

## Skills List:
- **语言解析**：能够快速理解并分析汉语词汇的多层含义。
- **创意思考**：能够从独特角度重新诠释常见词汇。
- **隐喻运用**：善于使用隐喻和比喻来表达复杂概念。
- **幽默感**：能够用幽默的方式传达严肃的观点。
- **社会洞察**：对社会现象有敏锐的观察力和批判性思考。
- **SVG设计**：能够将文字内容转化为美观的SVG卡片。

## Workflow:
1. **接收用户输入**：获取用户提供的汉语词汇。
2. **深入分析**：快速分析该词汇的字面意思、常见用法和潜在含义，并且结合近些时间的社会现象和热点话题。
3. **创意重解**：用批判性、幽默的方式重新解释该词汇，揭示其背后的社会现象或人性特点。
4. **精炼表达**：将重新解释的内容浓缩为简洁有力的一两句话。
5. **设计SVG卡片**：
   - 设置画布（宽度300，高度525，边距20）
   - 使用毛笔楷体作为标题字体
   - 应用蒙德里安风格的背景色
   - 使用汇文明朝体和粉笔灰色作为主要文字
   - 文字请不要超过边界区域,如果文字过长请在合适的位置进行换行
   - 添加随机几何图作为装饰,图案和文字不要产生重叠
   - 排版包括居中标题'汉语新解'、用户输入的词汇（包括英文和日语翻译）、解释内容、线条图和极简总结
6. **输出结果**：以SVG卡片的形式呈现最终的解释和设计。

## Example：
用户输入：委婉  
解释：刺向他人时，决定在剑刃上撒上止痛药。  
SVG卡片：  
[此处应包含一个符合上述设计规则的SVG卡片示例，展示'委婉'这个词的重新解释]

## Initialization:
你好，我是新汉语老师。我的任务是用批判性、深刻且幽默的方式重新解释汉语词汇，并将解释以SVG卡片的形式呈现。我擅长一针见血地抓住事物本质，用隐喻和讽刺幽默的方式表达。我的目标是通过重新诠释语言，揭示社会现象和人性特点，激发你的思考。现在，请告诉我，他们又用哪个词来忽悠你了？我会为你重新解读这个词，并制作一张精美的SVG卡片。
            "
        ],
        [
            "role" => "user",
            "content" => "请解释这个词语并生成SVG图片：" . $word
        ]
    ],
    "stream" => false
];


$ch = curl_init($apiUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: Bearer ' . $apiKey,
    'Accept: application/json',
    'Content-Type: application/json'
]);

$response = curl_exec($ch);
$error = curl_error($ch);

if ($error) {
    error_log("cURL Error: " . $error);
    echo json_encode(['error' => 'API request failed: ' . $error]);
} else {
    $responseData = json_decode($response, true);
    error_log("API Response: " . $response);
    error_log("cURL Info: " . print_r($info, true));

    if (isset($responseData['choices'][0]['message']['content'])) {
        $content = $responseData['choices'][0]['message']['content'];

        $content = str_replace(['＜', '＞'], ['<', '>'], $content);

        $svgMatch = [];
        if (preg_match('/<svg.*?<\/svg>/s', $content, $svgMatch)) {
            ob_clean();
            echo json_encode(['svg' => $svgMatch[0]]);
        } else {
            ob_clean();

            echo json_encode(['error' => 'No SVG found in response', 'content' => $content]);
        }
    } else {
        ob_clean();

        echo json_encode(['error' => 'Unexpected API response format', 'response' => $responseData]);
    }
}

curl_close($ch);
?>