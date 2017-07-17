<?php

namespace V3s3Bundle\Controller;

use finfo;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;

use V3s3Bundle\Entity\V3s3;
use V3s3Bundle\Helper\V3s3Html;
use V3s3Bundle\Helper\V3s3Xml;
use V3s3Bundle\Exception\V3s3InputValidationException;

class DefaultController extends Controller {
	const TRANSLATION_DOMAIN = 'V3s3';
	/**
	 * @Route("/{name}", requirements={"name"=".+"}, name="v3s3_put")
	 * @Method("PUT")
	 */
	// 1. VALIDATE PUT INPUT. RETURN TRUE IF SUCCESSFUL OR JSON RESPONSE ON ERROR.
	// 2. GET ROUTE PARAMETER OR REQUEST URL PATH. (as $name, already provided by controller action method dependency injection argument)
	// 3. GET RAW REQUEST INPUT BODY CONTENT. (as $data)
	// 4. GET SINGLE REQUEST HEADER. (use the Content-Type request header or attempt to determine the MIME type from $data using PHP's finfo as $mime_type)
	// 5. GET REQUEST CLIENT IP ADDRESS.
	// 6. INSERT ENTITY FROM PUT REQUEST AND RETURN THE ENTITY ARRAY. (pass the obtained values to the table gateway for insertion into the database)
	// 7. SET SINGLE RESPONSE HEADER. RETURN JSON RESPONSE. (v3s3-object-id: ID of inserted entity row)
	public function putAction($name, Request $request) {
		/* TASK: VALIDATE PUT INPUT. RETURN TRUE IF SUCCESSFUL OR JSON RESPONSE ON ERROR. */
		try {
			if (empty($name) || ($name == '/')) {
				throw new V3s3InputValidationException(
					$this->get('translator')->trans('V3S3_EXCEPTION_PUT_EMPTY_OBJECT_NAME', [], self::TRANSLATION_DOMAIN),
					V3s3InputValidationException::PUT_EMPTY_OBJECT_NAME
				);
			} else if (strlen($name) > 1024) {
				throw new V3s3InputValidationException(
					$this->get('translator')->trans('V3S3_EXCEPTION_OBJECT_NAME_TOO_LONG', [], self::TRANSLATION_DOMAIN),
					V3s3InputValidationException::OBJECT_NAME_TOO_LONG
				);
			}
		} catch(V3s3InputValidationException $e) {
			return new JsonResponse(
				[
					[
						'status'=>0,
						'code'=>$e->getCode(),
						'message'=>$e->getMessage()
					]
				]
			);
		}
		/* END: VALIDATE PUT INPUT. RETURN TRUE IF SUCCESSFUL OR JSON RESPONSE ON ERROR. */

		$data =
			/* TASK: GET RAW REQUEST INPUT BODY CONTENT. */
			$request->getContent();
			/* END: GET RAW REQUEST INPUT BODY CONTENT. */

		$content_type =
			/* TASK: GET SINGLE REQUEST HEADER. */
			$request->headers->get('Content-Type');
			/* END: GET SINGLE REQUEST HEADER. */

		$mime_type = (is_null($content_type)?(new finfo(FILEINFO_MIME))->buffer($data):$content_type);

			/* TASK: INSERT ENTITY FROM PUT REQUEST AND RETURN THE ENTITY ARRAY. */
			$em = $this->getDoctrine()->getManager();
			$tableGateway = $em->getRepository(V3s3::class);

		$entity =
			$tableGateway->put(
				[
					'ip'=>
						/* TASK: GET REQUEST CLIENT IP ADDRESS. */
						$request->getClientIp(),
						/* END: GET REQUEST CLIENT IP ADDRESS. */
					'name'=>$name,
					'data'=>$data,
					'mime_type'=>$mime_type,
				]
			);
			/* TASK: INSERT ENTITY FROM PUT REQUEST AND RETURN THE ENTITY ARRAY. */

		$response =
			/* TASK: SET SINGLE RESPONSE HEADER. RETURN JSON RESPONSE. */
			new JsonResponse(
				[
					'status'=>1,
					'message'=>$this->get('translator')->trans('V3S3_MESSAGE_PUT_OBJECT_ADDED_SUCCESSFULLY', [], self::TRANSLATION_DOMAIN)
				],
				200,
				['v3s3-object-id'=>$entity->getid()]
			);
			/* TASK: SET SINGLE RESPONSE HEADER. RETURN JSON RESPONSE. */

		return $response;
	}

	/**
	 * @Route("/{name}", requirements={"name"=".+"}, name="v3s3_get")
	 * @Method("GET")
	 */
	// 1. VALIDATE GET INPUT. RETURN TRUE IF SUCCESSFUL OR JSON RESPONSE ON ERROR.
	// 2. GET ROUTE PARAMETER OR REQUEST URL PATH. (as $name, already provided by controller action method dependency injection argument)
	// 3. GET ALL URL QUERY PARAMETERS AS ARRAY. (as $input)
	// 4. SELECT ONE SINGLE ENTITY RESULT FROM PERMITTED GET REQUEST URL PARAMETERS AND RETURN THE ENTITY AS ARRAY OR FALSE IF NOT FOUND.
	// {if such entity has been found}
	//  5.1.1. ASSIGN STRING VALUE AS THE RESPONSE OUTPUT BODY CONTENT. (send the entity data column contents as response body)
	//  5.1.2. SEND MULTIPLE RESPONSE HEADERS FROM ARRAY. OVERWRITE EXISTING KEYS. (send the v3s3-object-id (entity ID), Content-Type and Content-Length HTTP headers)
	//  5.1.3. GET SINGLE REQUEST URL QUERY PARAMETER.
	//  5.1.4. SET RESPONSE ATTACHMENT DOWNLOAD HEADER. (if the "download" GET request URL parameter is not empty set the Content-Disposition HTTP header)
	//  5.1.5. GET RESPONSE OBJECT. (and return it to the container)
	// {else}
	//  5.2 SET RESPONSE STATUS. RETURN JSON RESPONSE. (respond with a 404 status and a JSON result)
	// {endif}
	public function getAction($name, Request $request) {
		/* TASK: VALIDATE GET INPUT. RETURN TRUE IF SUCCESSFUL OR JSON RESPONSE ON ERROR. */
		try {
			if (empty($name) || ($name == '/')) {
				throw new V3s3InputValidationException(
					$this->get('translator')->trans('V3S3_EXCEPTION_GET_EMPTY_OBJECT_NAME', [], self::TRANSLATION_DOMAIN),
					V3s3InputValidationException::PUT_EMPTY_OBJECT_NAME
				);
			} else if (strlen($name) > 1024) {
				throw new V3s3InputValidationException(
					$this->get('translator')->trans('V3S3_EXCEPTION_OBJECT_NAME_TOO_LONG', [], self::TRANSLATION_DOMAIN),
					V3s3InputValidationException::OBJECT_NAME_TOO_LONG
				);
			}
		} catch(V3s3InputValidationException $e) {
			return new JsonResponse(
				[
					[
						'status'=>0,
						'code'=>$e->getCode(),
						'message'=>$e->getMessage()
					]
				]
			);
		}
		/* END: VALIDATE GET INPUT. RETURN TRUE IF SUCCESSFUL OR JSON RESPONSE ON ERROR. */

		$input =
			/* TASK: GET ALL URL QUERY PARAMETERS AS ARRAY. */
			$request->query->all();
			/* END: GET ALL URL QUERY PARAMETERS AS ARRAY. */

			/* TASK: SELECT ONE SINGLE ENTITY RESULT FROM PERMITTED GET REQUEST URL PARAMETERS AND RETURN THE ENTITY AS ARRAY OR FALSE IF NOT FOUND. */
			$entityManager = $this->getDoctrine()->getManager();
			$tableGateway = $entityManager->getRepository(V3s3::class);

		$entity =
			$tableGateway->get(
				array_replace(
					$input,
					[
						'name'=>$name,
					]
				)
			);
			/* TASK: SELECT ONE SINGLE ENTITY RESULT FROM PERMITTED GET REQUEST URL PARAMETERS AND RETURN THE ENTITY AS ARRAY OR FALSE IF NOT FOUND. */

		if(!empty($entity) && !$entity->isDeleted()) {
			if(empty($entity->getmime_type())) {
				$entity->setmime_type((new finfo(FILEINFO_MIME))->buffer($entity->getdata()));
			}

			$response =
				/* TASK: ASSIGN STRING VALUE AS THE RESPONSE OUTPUT BODY CONTENT. */
				new Response(
					$entity->getdata(),
					200,
					[
						'v3s3-object-id'=>$entity->getid(),
						'Content-Type'=>$entity->getmime_type(),
						'Content-Length'=>strlen($entity->getdata())
					]
				);
				/* TASK: ASSIGN STRING VALUE AS THE RESPONSE OUTPUT BODY CONTENT. */

			if(
				!empty(
					/* TASK: GET SINGLE REQUEST URL QUERY PARAMETER. */
					$request->query->get('download')
					/* END: GET SINGLE REQUEST URL QUERY PARAMETER. */
				)
			) {
				$filename = basename($name);

				/* TASK: SET RESPONSE ATTACHMENT DOWNLOAD HEADER. */
				$response->headers->set(
					'Content-Disposition',
					$response->headers->makeDisposition('attachment', $filename)
				);
				/* END: SET RESPONSE ATTACHMENT DOWNLOAD HEADER. */
			}

			return $response;
		} else {
			return
				/* TASK: SET RESPONSE STATUS. RETURN JSON RESPONSE. */
				new JsonResponse(
					[
						'status'=>1,
						'results'=>0,
						'message'=>$this->get('translator')->trans('V3S3_MESSAGE_404', [], self::TRANSLATION_DOMAIN) // TASK: TRANSLATE THE GIVEN STRING USING CURRENT LOCALE LANGUAGE AND THE PROVIDED TRANSLATOR MECHANISM AND TRANSLATION FILES.
					],
					404
				);
				/* TASK: SET RESPONSE STATUS. RETURN JSON RESPONSE. */
		}
	}

	/**
	 * @Route("/{name}", requirements={"name"=".+"}, name="v3s3_delete")
	 * @Method("DELETE")
	 */
	// 1. VALIDATE DELETE INPUT. RETURN TRUE IF SUCCESSFUL OR JSON RESPONSE ON ERROR.
	// 2. GET ALL URL QUERY PARAMETERS AS ARRAY.
	// 3. GET ROUTE PARAMETER OR REQUEST URL PATH. (as $name)
	// 4. SELECT ENTITY FROM PERMITTED DELETE REQUEST PARAMETERS, UPDATE THE STATUS FIELD TO DELETED AND RETURN THE UPDATED ENTITY AS ARRAY OR FALSE IF NOT FOUND.
	// {if no such entity has been found}
	//  5.1. SET RESPONSE STATUS. RETURN JSON RESPONSE. (return 404 status and a JSON response)
	// {else}
	//  5.2. SET SINGLE RESPONSE HEADER. RETURN JSON RESPONSE. (return a v3s3-object-id (ID of the deleted entity) header and a JSON response)
	// {endif}
	public function deleteAction($name, Request $request) {
		/* TASK: VALIDATE DELETE INPUT. RETURN TRUE IF SUCCESSFUL OR JSON RESPONSE ON ERROR. */
		try {
			if (empty($name) || ($name == '/')) {
				throw new V3s3InputValidationException(
					$this->get('translator')->trans('V3S3_EXCEPTION_DELETE_EMPTY_OBJECT_NAME', [], self::TRANSLATION_DOMAIN),
					V3s3InputValidationException::PUT_EMPTY_OBJECT_NAME
				);
			} else if (strlen($name) > 1024) {
				throw new V3s3InputValidationException(
					$this->get('translator')->trans('V3S3_EXCEPTION_OBJECT_NAME_TOO_LONG', [], self::TRANSLATION_DOMAIN),
					V3s3InputValidationException::OBJECT_NAME_TOO_LONG
				);
			}
		} catch(V3s3InputValidationException $e) {
			return new JsonResponse(
				[
					[
						'status'=>0,
						'code'=>$e->getCode(),
						'message'=>$e->getMessage()
					]
				]
			);
		}
		/* END: VALIDATE DELETE INPUT. RETURN TRUE IF SUCCESSFUL OR JSON RESPONSE ON ERROR. */

		$input =
			/* TASK: GET ALL URL QUERY PARAMETERS AS ARRAY. */
			$request->query->all();
			/* END: GET ALL URL QUERY PARAMETERS AS ARRAY. */

			/* TASK: SELECT ENTITY FROM PERMITTED DELETE REQUEST PARAMETERS, UPDATE THE STATUS FIELD TO DELETED AND RETURN THE UPDATED ENTITY AS ARRAY OR FALSE IF NOT FOUND. */
			$entityManager = $this->getDoctrine()->getManager();
			$tableGateway = $entityManager->getRepository(V3s3::class);

		$entity =
			$tableGateway->api_delete(
				array_replace(
					$input,
					[
						'name'=>$name,
						'ip_deleted_from'=>$request->getClientIp()
					]
				)
			);
			/* END: SELECT ENTITY FROM PERMITTED DELETE REQUEST PARAMETERS, UPDATE THE STATUS FIELD TO DELETED AND RETURN THE UPDATED ENTITY AS ARRAY OR FALSE IF NOT FOUND. */

		if(empty($entity)) {
			return
				/* TASK: SET RESPONSE STATUS. RETURN JSON RESPONSE. */
				new JsonResponse(
					[
						'status'=>1,
						'results'=>0,
						'message'=>$this->get('translator')->trans('V3S3_MESSAGE_NO_MATCHING_RESOURCES', [], self::TRANSLATION_DOMAIN)
					],
					404
				);
				/* END: SET RESPONSE STATUS. RETURN JSON RESPONSE. */
		} else {
			return
				/* TASK: SET SINGLE RESPONSE HEADER. RETURN JSON RESPONSE. */
				new JsonResponse(
					[
						'status'=>1,
						'results'=>1,
						'message'=>$this->get('translator')->trans('V3S3_MESSAGE_DELETE_OBJECT_DELETED_SUCCESSFULLY', [], self::TRANSLATION_DOMAIN)
					],
					200,
					[
						'v3s3-object-id'=>$entity->getid()
					]
				);
				/* END: SET SINGLE RESPONSE HEADER. RETURN JSON RESPONSE. */
		}
	}

	/**
	 * @Route("/{name}", requirements={"name"=".+"}, name="v3s3_post")
	 * @Method("POST")
	 */
	// 1. GET RAW REQUEST BODY CONTENT AS STRING.
	// 2. VALIDATE RAW POST REQUEST BODY CONTENT INPUT AND PARSE IT AS JSON. RETURN JSON RESPONSE ON ERROR.
	// 3. GET ROUTE PARAMETER OR REQUEST URL PATH. (as $name)
	// 4. SELECT MULTIPLE ENTITIES RESULT SET FROM PERMITTED PARAMETERS FROM THE POST JSON FILTER KEY AND RETURN THE RESULT SET AS ARRAY.
	// {if at least one entity has been found}
	//  {if format is xml}
	//   5.1.1. CREATE XML RESPONSE.
	//  {else if format is html}
	//   5.1.2. CREATE HTML RESPONSE.
	//  {else}
	//   5.1.3. CREATE JSON RESPONSE WITH PRETTY PRINT.
	//  {endif}
	// {else}
	//  5.2. CREATE JSON RESPONSE
	// {endif}
	public function postAction($name, Request $request) {
		$input =
			/* TASK: GET RAW REQUEST BODY CONTENT AS STRING. */
			$request->getContent();
			/* END: GET RAW REQUEST BODY CONTENT AS STRING. */

		/* TASK: VALIDATE RAW POST REQUEST BODY CONTENT INPUT AND PARSE IT AS JSON. RETURN JSON RESPONSE ON ERROR. */
		$serializer = new Serializer([], [new JsonEncoder]);
		$parsed_input = $serializer->decode($input, 'json');

		if(!empty($input) && empty($parsed_input)) {
			try {
				throw new V3s3InputValidationException(
					$this->get('translator')->trans('V3S3_EXCEPTION_POST_INVALID_REQUEST', [], self::TRANSLATION_DOMAIN),
					V3s3InputValidationException::POST_INVALID_REQUEST
				);
			} catch(V3s3InputValidationException $e) {
				return new JsonResponse(
					[
						[
							'status'=>0,
							'code'=>$e->getCode(),
							'message'=>$e->getMessage()
						]
					]
				);
			}
		}
		/* END: VALIDATE RAW POST REQUEST BODY CONTENT INPUT AND PARSE IT AS JSON. RETURN JSON RESPONSE ON ERROR. */

		$attr = (!empty($parsed_input['filter'])?$parsed_input['filter']:[]);
		if(!empty($name) && ($name != '/')) {
			$attr['name'] = $name;
		}

			/* TASK: SELECT MULTIPLE ENTITIES RESULT SET FROM PERMITTED PARAMETERS FROM THE POST JSON FILTER KEY AND RETURN THE RESULT SET AS ARRAY. */
			$entityManager = $this->getDoctrine()->getManager();
			$tableGateway = $entityManager->getRepository(V3s3::class);

		$entityResultSet =
			$tableGateway->post($attr);
			/* END: SELECT MULTIPLE ENTITIES RESULT SET FROM PERMITTED PARAMETERS FROM THE POST JSON FILTER KEY AND RETURN THE RESULT SET AS ARRAY. */

		if(!empty($entityResultSet)) {
			// remove irrelevant columns from the result and format others accordingly
			foreach ($entityResultSet as &$_row) {
				$_row = $_row->castEntityObjectToArray();
				unset($_row['id']);
				unset($_row['timestamp']);
				unset($_row['hash_name']);
				unset($_row['timestamp_deleted']);
				if(empty($_row['mime_type'])) {
					$_row['mime_type'] = (new finfo(FILEINFO_MIME))->buffer($_row['data']).' (determined using PHP finfo)';
				}
				$_row['data'] = (new finfo(FILEINFO_MIME))->buffer($_row['data']);
			}

			$format = ((!empty($parsed_input['format'])&&in_array($parsed_input['format'], ['json', 'xml', 'html']))?strtolower($parsed_input['format']):'json');
			switch($format) {
				case 'xml':
					/* TASK: CREATE XML RESPONSE. */
					return new Response(
						V3s3Xml::simple_xml($entityResultSet),
						200,
						['Content-Type'=>'text/xml; charset=utf-8']
					);
					/* END: CREATE XML RESPONSE. */
					break;
				case 'html':
					/* TASK: CREATE HTML RESPONSE. */
					return new Response(
						V3s3Html::simple_table($entityResultSet),
						200,
						['Content-Type'=>'text/html; charset=utf-8']
					);
					/* END: CREATE HTML RESPONSE. */
					break;
				case 'json':
				default:
					/* TASK: CREATE JSON RESPONSE WITH PRETTY PRINT. */
					$serializer = new Serializer([], [new JsonEncoder]);
					$json = $serializer->encode(
						$entityResultSet,
						'json',
						[
							'json_encode_options'=>JSON_PRETTY_PRINT
						]
					);
					return new JsonResponse(
						$json,
						200,
						[],
						true
					);
					/* END: CREATE JSON RESPONSE WITH PRETTY PRINT. */
					break;
			}
		} else {
			return
				/* TASK: CREATE JSON RESPONSE. */
				new JsonResponse(
					[
						'status'=>1,
						'results'=>0,
						'message'=>$this->get('translator')->trans('V3S3_MESSAGE_NO_MATCHING_RESOURCES', [], self::TRANSLATION_DOMAIN)
					]
				);
				/* END: CREATE JSON RESPONSE. */
		}
	}
}