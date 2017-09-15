{if $solution_version->exists() && !is_null($solution_version->comment)}
<div class="solution_version_comment"><strong>{$solution_version->solution_student_fullname}:</strong> {$solution_version->comment|nl2br}</div>
{/if}
