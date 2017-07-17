<?php

namespace V3s3Bundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;

use V3s3Bundle\Entity\V3s3;

class V3s3Repository extends EntityRepository {
	// 1. GET ALL OR FILLABLE ENTITY PROPERTIES OR TABLE COLUMNS AS ARRAY.
	// 2. INSERT ENTITY INTO STORAGE AND RETURN THE ENTITY OBJECT.
	public function put(Array $attr) {
			/* TASK: GET ALL OR FILLABLE ENTITY PROPERTIES OR TABLE COLUMNS AS ARRAY. */
			$columns = $this->getClassMetadata()->getFieldNames();
		$columns =
			array_combine($columns, $columns);
			/* END: GET ALL OR FILLABLE ENTITY PROPERTIES OR TABLE COLUMNS AS ARRAY. */

		$attr = array_intersect_key(
			$attr,
			$columns
		);
		$attr['timestamp'] = (isset($attr['timestamp'])?$attr['timestamp']:time());
		$attr['date_time'] = date('Y-m-d H:i:s O', $attr['timestamp']);
		if(isset($attr['name'])) {
			$attr['hash_name'] = sha1($attr['name']);
		} else {
			unset($attr['hash_name']);
		}
		$attr['status'] = (isset($attr['status'])?$attr['status']:V3s3::STATUS_ACTIVE);
		unset($attr['id']);

		/* TASK: INSERT ENTITY INTO STORAGE AND RETURN THE ENTITY OBJECT. */
		$entity = new V3s3($attr);

		$em = $this->getEntityManager();
		$em->persist($entity);
		$em->flush();

		return $entity;
		/* END: INSERT ENTITY INTO STORAGE AND RETURN THE ENTITY OBJECT. */
	}

	// 1. GET ALL OR FILLABLE ENTITY PROPERTIES OR TABLE COLUMNS AS ARRAY.
	// 2. FETCH ONE SINGLE ENTITY RESULT SET.
	// 3. GET FETCH RESULT ROW COUNT.
	// 4. GET ONE SINGLE FIRST OR CURRENT ENTITY OBJECT FROM RESULT SET.
	public function get(Array $attr) {
			/* TASK: GET ALL OR FILLABLE ENTITY PROPERTIES OR TABLE COLUMNS AS ARRAY. */
			$columns = $this->getClassMetadata()->getFieldNames();
		$columns =
			array_combine($columns, $columns);
			/* END: GET ALL OR FILLABLE ENTITY PROPERTIES OR TABLE COLUMNS AS ARRAY. */

		$attr = array_intersect_key(
			$attr,
			$columns
		);

		if(isset($attr['name'])) {
			$attr['hash_name'] = sha1($attr['name']);
		} else {
			unset($attr['hash_name']);
		}
		unset($attr['name']);


		$entityResultSet =
			/* TASK: FETCH ONE SINGLE ENTITY RESULT SET. */
			$this->findBy($attr, ['id'=>'DESC'], 1);
			/* END: FETCH ONE SINGLE ENTITY RESULT SET. */

		$rows_count =
			/* TASK: GET FETCH RESULT ROW COUNT. */
			count($entityResultSet);
			/* END: GET FETCH RESULT ROW COUNT. */

		if(empty($rows_count)) {
			return false;
		}

		return
			/* TASK: GET ONE SINGLE FIRST OR CURRENT ENTITY OBJECT FROM RESULT SET. */
			reset($entityResultSet);
			/* END: GET ONE SINGLE FIRST OR CURRENT ENTITY OBJECT FROM RESULT SET. */
	}

	// 1. GET ALL OR FILLABLE ENTITY PROPERTIES OR TABLE COLUMNS AS ARRAY.
	// 2. FETCH ONE SINGLE ENTITY RESULT SET.
	// 3. GET FETCH RESULT ROW COUNT.
	// 4. GET ONE SINGLE FIRST OR CURRENT ENTITY OBJECT FROM RESULT SET.
	// 5. UPDATE ENTITY IN STORAGE AND RETURN THE ENTITY AS ARRAY.
	public function api_delete(Array $attr) {
			/* TASK: GET ALL OR FILLABLE ENTITY PROPERTIES OR TABLE COLUMNS AS ARRAY. */
			$columns = $this->getClassMetadata()->getFieldNames();
		$columns =
			array_combine($columns, $columns);
			/* END: GET ALL OR FILLABLE ENTITY PROPERTIES OR TABLE COLUMNS AS ARRAY. */

		$attr = array_intersect_key(
			$attr,
			$columns
		);

		$attr['timestamp_deleted'] = (isset($attr['timestamp_deleted'])?$attr['timestamp_deleted']:time());
		$attr['date_time_deleted'] = date('Y-m-d H:i:s O', $attr['timestamp_deleted']);
		if(isset($attr['name'])) {
			$attr['hash_name'] = sha1($attr['name']);
		} else {
			unset($attr['hash_name']);
		}
		$attr['status'] = (isset($attr['status'])?$attr['status']:V3s3::STATUS_DELETED);
		unset($attr['name']);

		$where = $attr;
		unset($where['status']);
		unset($where['timestamp_deleted']);
		unset($where['date_time_deleted']);
		unset($where['ip_deleted_from']);

		$entityResultSet =
			/* TASK: FETCH ONE SINGLE ENTITY RESULT SET. */
			$this->findBy($where, ['id'=>'DESC'], 1);
			/* END: FETCH ONE SINGLE ENTITY RESULT SET. */

		$rows_count =
			/* TASK: GET FETCH RESULT ROW COUNT. */
			count($entityResultSet);
			/* END: GET FETCH RESULT ROW COUNT. */

		if(empty($rows_count)) {
			return false;
		}

		$entity =
			/* TASK: GET ONE SINGLE FIRST OR CURRENT ENTITY OBJECT FROM RESULT SET. */
			reset($entityResultSet);
			/* END: GET ONE SINGLE FIRST OR CURRENT ENTITY OBJECT FROM RESULT SET. */

		/* TASK: UPDATE ENTITY IN STORAGE AND RETURN THE ENTITY AS ARRAY. */
		$entity->fromArray($attr);

		$em = $this->getEntityManager();
		$em->flush();

		return $entity;
		/* TASK: UPDATE ENTITY IN STORAGE AND RETURN THE ENTITY AS ARRAY. */
	}

	// 1. GET ALL OR FILLABLE ENTITY PROPERTIES OR TABLE COLUMNS AS ARRAY.
	// 2. FETCH MULTIPLE ENTITIES RESULT SET.
	// 3. GET FETCH RESULT ROW COUNT.
	public function post(Array $attr) {
			/* TASK: GET ALL OR FILLABLE ENTITY PROPERTIES OR TABLE COLUMNS AS ARRAY. */
			$columns = $this->getClassMetadata()->getFieldNames();
		$columns =
			array_combine($columns, $columns);
			/* END: GET ALL OR FILLABLE ENTITY PROPERTIES OR TABLE COLUMNS AS ARRAY. */

		$attr = array_intersect_key(
			$attr,
			$columns
		);

		if(isset($attr['name'])) {
			$attr['hash_name'] = sha1($attr['name']);
		} else {
			unset($attr['hash_name']);
		}
		unset($attr['name']);

		$entityResultSet =
			/* TASK: FETCH MULTIPLE ENTITIES RESULT SET. */
			$this->findBy($attr);
			/* END: FETCH MULTIPLE ENTITIES RESULT SET. */

		$rows_count =
			/* TASK: GET FETCH RESULT ROW COUNT. */
			count($entityResultSet);
			/* END: GET FETCH RESULT ROW COUNT. */

		return (!empty($rows_count)?$entityResultSet:[]);
	}
}
