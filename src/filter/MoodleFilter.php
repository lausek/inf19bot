<?

require_once __DIR__ . '/../../vendor/autoload.php';

use League\HTMLToMarkdown\HtmlConverter;

class MoodleFilter
{
    function is_forward_allowed($mail) : bool
    {
        $subject = trim($mail->subject);
        return strpos($subject, Util::get_config('course')) === 0;
    }

    function format_msg($mail)
    {
        if ($mail->textHtml)
        {
            $msg = $this->normalize_html($mail->textHtml);
        }
        else
        {
            $msg = $mail->textPlain;
        }

        return $this->strip_footer($msg);
    }

    // remove google groups email footer
    function strip_footer(string $msg): string
    {
        // if email has no footer, return original
        if (false === strpos($msg, '--'))
        {
            return $msg;
        }
        $parts = explode('--', $msg);
        return implode('--', $parts);
    }

    function normalize_html(string $html): string
    {
        $converter = new HtmlConverter();
        $converter->getConfig()->setOption('strip_tags', true);
        $md = $converter->convert($html);
        return $md;
    }
}
