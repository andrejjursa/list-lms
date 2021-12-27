<?php

/**
 * Changelog file parser.
 *
 * @package LIST_Libraries
 * @author  Andrej Jursa
 * @version 1.0
 */
class Changelog
{
    
    protected $file_content = null;
    
    protected $parsed_content = null;
    
    /**
     * @throws Exception
     */
    public function read($filename): void
    {
        if (file_exists($filename)) {
            $this->file_content = file_get_contents($filename);
            $this->parsed_content = null;
        } else {
            $this->file_content = null;
            $this->parsed_content = null;
            throw new Exception('Changelog file not.');
        }
    }
    
    /**
     * @throws Exception
     */
    public function parse(): void
    {
        if (is_null($this->file_content)) {
            throw new Exception('Changelog file content not readed.');
        }
        $this->parsed_content = [];
        
        $lines = explode("\n", $this->file_content);
        
        $current_version = null;
        $version_date = null;
        $version_lines = [];
        $first_line_number = 0;
        
        $line_number = 1;
        
        try {
            foreach ($lines as $line) {
                $line = rtrim($line);
                if (is_null($current_version)
                    && preg_match(
                        '/^VERSION (?P<version>[0-9]+\.[0-9]+\.[0-9]+[a-z]*) \| (?P<date>[0-9]{4}-[0-9]{2}-[0-9]{2})/',
                        $line,
                        $matches
                    )
                ) {
                    $current_version = $matches['version'];
                    $version_date = $matches['date'];
                    $version_lines = [];
                    $first_line_number = $line_number + 1;
                } else if (!is_null($current_version) && preg_match('/^END VERSION/', $line)) {
                    $this->parsed_content[$current_version]['date'] = $version_date;
                    $this->parsed_content[$current_version]['reports'] =
                        $this->parse_version($version_lines, $first_line_number);
                    $current_version = null;
                } else if (!is_null($current_version)) {
                    $version_lines[] = $line;
                } else if (is_null($current_version) && (mb_substr($line, 0, 1) !== '#'
                        && trim($line) !== '')
                ) {
                    throw new exception('Unexpected input on line ' . $line_number . ': ' . $line);
                }
                $line_number++;
            }
        } catch (Exception $e) {
            $this->parsed_content = null;
            throw $e;
        }
    }
    
    public function get($version = null)
    {
        if (is_null($version)) {
            return $this->parsed_content;
        }
        
        return $this->parsed_content[$version] ?? null;
    }
    
    /**
     * @throws exception
     */
    protected function parse_version($lines, $line_number): array
    {
        $output = [];
        
        $in_block = false;
        
        $last_command = null;
        
        $ml_command_text = '';
        $ml_command_type = '';
        $ml_command_lang_overlay = '';
        $ml_command_line_number = 0;
        
        if (count($lines)) {
            foreach ($lines as $line) {
                if (!$in_block
                    && preg_match(
                        '/^(?P<command>(NEW|FIX|CHANGE|REMOVE))( \[lang:(?P<lang>[a-z]+)\])? (?P<text>.*)/',
                        $line,
                        $matches
                    )
                ) {
                    if (!isset($matches['lang']) || $matches['lang'] === '') {
                        $last_command = new ChangelogNode($matches['command'], $matches['text']);
                        $output[] = $last_command;
                    } else if (!is_null($last_command) && $last_command->getType() === $matches['command']) {
                        $last_command->addLangOverlay($matches['lang'], $matches['text']);
                    } else {
                        throw new exception(
                            'Unexpected language overlay definition for command ' . $matches['command']
                            . ' on line ' . $line_number . '.'
                        );
                    }
                } else if (!$in_block
                    && preg_match(
                        '/^ML (?P<command>(NEW|FIX|CHANGE|REMOVE))( \[lang:(?P<lang>[a-z]+)\])?/',
                        $line,
                        $matches
                    )
                ) {
                    $ml_command_text = '';
                    $ml_command_type = $matches['command'];
                    $ml_command_lang_overlay = $matches['lang'] ?? null;
                    $ml_command_line_number = $line_number;
                    $in_block = true;
                } else if ($in_block
                    && preg_match('/^END (?P<command>(NEW|FIX|CHANGE|REMOVE))/', $line, $matches)
                ) {
                    if ($matches['command'] === $ml_command_type) {
                        $in_block = false;
                        if (is_null($ml_command_lang_overlay) || $ml_command_lang_overlay === '') {
                            $last_command = new ChangelogNode($ml_command_type, $ml_command_text);
                            $output[] = $last_command;
                        } else if (!is_null($last_command) && $last_command->getType() === $ml_command_type) {
                            $last_command->addLangOverlay($ml_command_lang_overlay, $ml_command_text);
                        } else {
                            throw new exception(
                                'Unexpected language overlay definition for command ' . $ml_command_type
                                . ' on line ' . $ml_command_line_number . '.'
                            );
                        }
                    } else {
                        throw new Exception(
                            'Found end of block for command ' . $matches['command'] . ' on line '
                            . $line_number . ' but block for command ' . $ml_command_type . ' is open.'
                        );
                    }
                } else if ($in_block) {
                    if (trim($line) !== '') {
                        $ml_command_text .= ($ml_command_text !== '' ? PHP_EOL : '') . $line;
                    }
                } else if (!$in_block && (mb_substr($line, 0, 1) !== '#' && trim($line) !== '')) {
                    throw new exception('Unexpected input on line ' . $line_number . ': ' . $line);
                }
                
                $line_number++;
            }
        }
        
        if ($in_block) {
            throw new Exception(
                'Block for command ' . $ml_command_type . ' starting on line '
                . $ml_command_line_number . ' not closed properly.'
            );
        }
        
        return $output;
    }
    
}

class ChangelogNode
{
    
    const TYPE_NEW = 'NEW';
    const TYPE_CHANGE = 'CHANGE';
    const TYPE_FIX = 'FIX';
    const TYPE_REMOVE = 'REMOVE';
    
    protected $texts = [];
    protected $type = self::TYPE_NEW;
    
    public function __construct($type = self::TYPE_NEW, $text = '')
    {
        $this->type = $type;
        $this->texts = [
            'default' => $text,
        ];
    }
    
    public function addLangOverlay($lang, $text = ''): void
    {
        if (trim($lang) !== '' && trim($text) !== '') {
            $this->texts['langs'][trim($lang)] = trim($text);
        }
    }
    
    public function getType()
    {
        return $this->type;
    }
    
    public function getText($lang = null)
    {
        if (is_null($lang)) {
            return $this->texts['default'];
        }
        
        return $this->texts['langs'][$lang] ?? $this->texts['default'];
    }
}