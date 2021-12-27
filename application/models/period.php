<?php

use Application\Interfaces\DataMapperExtensionsInterface;

/**
 * Period model.
 *
 * @property int    $id
 * @property string $updated    date time format YYYY-MM-DD HH:MM:SS
 * @property string $created    date time format YYYY-MM-DD HH:MM:SS
 * @property string $name
 * @property int    $sorting
 * @property Course $course
 *
 * @method DataMapper where_related_course(mixed $related, string $field = null, string $value = null)
 *
 * @package LIST_DM_Models
 * @author  Andrej Jursa
 *
 */
class Period extends DataMapper implements DataMapperExtensionsInterface
{
    
    public $has_many = [
        'course',
    ];
    
    /**
     * This method will move current period up in sorting order.
     */
    public function move_up(): void
    {
        if (isset($this->id) && $this->id > 0) {
            $up_period = new Period();
            $up_period->order_by('sorting', 'desc')->limit(1);
            $up_period->where('sorting <', $this->sorting);
            $up_period->get();
            if ($up_period->exists()) {
                $upsort = $up_period->sorting;
                $up_period->sorting = $this->sorting;
                if ($up_period->save()) {
                    $my_period = new Period();
                    $my_period->get_where(['id' => $this->id]);
                    $my_period->sorting = $upsort;
                    if ($my_period->save()) {
                        $this->sorting = $upsort;
                    }
                }
            }
        }
    }
    
    /**
     * This method will move current period down in sorting order.
     */
    public function move_down(): void
    {
        if (isset($this->id) && $this->id > 0) {
            $down_period = new Period();
            $down_period->order_by('sorting', 'asc')->limit(1);
            $down_period->where('sorting >', $this->sorting);
            $down_period->get();
            if ($down_period->exists()) {
                $upsort = $down_period->sorting;
                $down_period->sorting = $this->sorting;
                if ($down_period->save()) {
                    $my_period = new Period();
                    $my_period->get_where(['id' => $this->id]);
                    $my_period->sorting = $upsort;
                    if ($my_period->save()) {
                        $this->sorting = $upsort;
                    }
                }
            }
        }
    }
    
    /**
     * Delete this period or related object.
     * If no parameters are set, this method deletes current period and re-sort all other periods.
     *
     * @param DataMapper|string $object        related object to delete from relation.
     * @param string            $related_field relation internal name.
     */
    public function delete($object = '', $related_field = '')
    {
        if (empty($object) && !is_array($object) && !empty($this->id)) {
            $lower_periods = new Period();
            $lower_periods->order_by('sorting', 'asc');
            $lower_periods->where('sorting > ', $this->sorting);
            $lower_periods->get_iterated();
            $ids = [];
            foreach ($lower_periods as $lower_period) {
                $ids[] = $lower_period->id;
            }
            if (count($ids) > 0) {
                $this->db->set('sorting', 'sorting-1', false);
                $this->db->where_in('id', $ids);
                $this->db->update('periods');
            }
        }
        parent::delete($object, $related_field);
    }
    
}