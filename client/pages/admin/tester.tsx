import { Modal, ModalOverlay, Button, StatHelpText, Stat, StatLabel, Popover, PopoverTrigger, PopoverContent, PopoverHeader, PopoverArrow, PopoverCloseButton, PopoverBody, PopoverFooter, StatNumber, ModalHeader, ModalCloseButton, ModalFooter, ModalContent, ModalBody, Box, Text, Table, Tr, Th, Thead, TableCaption, Tbody, Td, IconButton, Collapse, Grid, GridItem, RadioGroup, Stack, Radio, Select, ButtonGroup } from "@chakra-ui/react";
import { useEffect, useState } from "react";
import { BiSearch, BiTrash } from "react-icons/bi";
import Sidebar from "../../layouts/default";
import { ENDPOINT_URL } from "../../libs/utils";

function AnswerCard({ data }: any) {
	const answers = JSON.parse(data.answers)
	const originalAnswers = JSON.parse(data.originalAnswers)
	const [totalPoints, setTotalPoints] = useState(0)
	const [totalQuestions, setTotalQuestions] = useState(0)
	let tempTotalPoints = 0
	for (const oa of originalAnswers) {
		const answer = answers.find((a: any) => a.questionId == oa.id)
		if (answer) {
			if (answer.answer == oa.answer) {
				tempTotalPoints += 1
			}
		}
	}
	useEffect(() => {
		setTotalPoints(tempTotalPoints)
		setTotalQuestions(originalAnswers.length)
	}, [tempTotalPoints])
	return (
		<Box
			p='40px'
			color='black'
			border="1px"
			borderColor="gray.400"
			mt='1'
			bg='white'
			rounded='md'
			shadow='md'
		>
			<Grid templateColumns="repeat(4, 1fr)" gap={6}>
				<GridItem >
					<Stat>
						<StatLabel>ทั้งหมด</StatLabel>
						<StatNumber>{totalQuestions}</StatNumber>
						<StatHelpText>ข้อ</StatHelpText>
					</Stat>
				</GridItem>
				<GridItem>
					<Stat>
						<StatLabel>ตอบถูก</StatLabel>
						<StatNumber>{totalPoints}</StatNumber>
						<StatHelpText>ข้อ</StatHelpText>
					</Stat>
				</GridItem>
				<GridItem>
					<Stat>
						<StatLabel>ตอบผิด</StatLabel>
						<StatNumber>{
							totalQuestions - totalPoints
						}</StatNumber>
						<StatHelpText>ข้อ</StatHelpText>
					</Stat>
				</GridItem>
				<GridItem>
					<Stat>
						<StatLabel>ทำคะแนนได้</StatLabel>
						<StatNumber>{
							Math.round((totalPoints / totalQuestions) * 100)
						}</StatNumber>
						<StatHelpText>%</StatHelpText>
					</Stat>
				</GridItem>
			</Grid>
			<Box>
				<Text mt="4" fontSize="xl" fontWeight="bold">
					รายละเอียดการสอบ
				</Text>
			</Box>
			<Table variant='simple' mt="2">
				<Thead>
					<Tr>
						<Th>ข้อที่</Th>
						<Th>คำถาม</Th>
						<Th>คำตอบ</Th>
					</Tr>
				</Thead>
				<Tbody>
					{
						originalAnswers.map((item: any, index: number) => {
							return (
								<Tr key={index} bg={
									answers.find((a: any) => a.questionId === item.id)?.answer == item.answer ? 'green.100' : 'red.100'
								}>
									<Td>{
										index + 1
									}</Td>
									<Td>{
										item.text
									}</Td>
									<Td>
										<RadioGroup value={
											answers.find((a: any) => a.questionId === item.id)?.answer == item.answer ? '1' : '2'
										}>
											<Stack spacing="16px" direction="row">
												<Radio value="1">{
													item.choice1
												}</Radio>
												<Radio value="2">{
													item.choice2
												}</Radio>
											</Stack>
										</RadioGroup>
									</Td>
								</Tr>
							)
						})
					}
				</Tbody>
			</Table>
		</Box>
	)
}

function TesterPage() {
	const [modalContent, setModalContent] = useState('');
	const [targetCollapse, setTargetCollapse] = useState('');
	const [history, setHistory] = useState([])
	const [displayData, setDisplayData] = useState({} as any)
	const [selectFilter, setSelectFilter] = useState('all')
	const [exams, setExams] = useState([])
	const [isLoading, setIsLoading] = useState(false)
	const getAllExam = () => {
		fetch(ENDPOINT_URL + '/v1/public/exams', {
			method: 'GET',
			headers: {
				'Content-Type': 'application/json'
			}
		}).then(res => res.json()).then(res => {
			setExams(res)
		})
	}
	const getExamNameById = (id: string) => {
		const exam: any = exams.find((item: any) => item.id == id)
		if (exam) {
			return exam.name
		}
		return ''
	}
	const getResults = () => {
		fetch(ENDPOINT_URL + '/v1/admin/results', {
			method: 'GET',
			headers: {
				'Content-Type': 'application/json',
				'Authorization': 'Bearer ' + document.cookie.split('=')[1]
			}
		}).then(res => res.json()).then(res => {
			setHistory(res.filter((item: any) => item.submits.length > 0).sort(
				(a: any, b: any) => {
					const dateA = new Date(a.created_at);
					const dateB = new Date(b.created_at);
					return dateB.getTime() - dateA.getTime()
				}
			))
		})
	}
	useEffect(() => {
		getAllExam()
		getResults()
	}, []);

	const onDelelteButtonClick = (id: string) => {
		setIsLoading(true)
		fetch(ENDPOINT_URL + '/v1/admin/group/delete', {
			method: 'POST',
			body: JSON.stringify({
				groupId: id
			}),
			headers: {
				'Content-Type': 'application/json',
				'Authorization': 'Bearer ' + document.cookie.split('=')[1]
			}
		}).then(res => res.json()).then(res => {
			setHistory(history.filter((item: any) => item.id != id))
			setIsLoading(false)
			getAllExam()
			getResults()
		}).catch(err => {
			setIsLoading(false)
			getAllExam()
			getResults()
		})
	}

	useEffect(() => {
		if (modalContent) {
			setDisplayData(history.find((item: any) => item.id == modalContent))
		}
	}, [modalContent])
	return (
		<Sidebar>
			<Box>
				<Text fontSize="6xl">
					รายชื่อผู้ทำแบบทดสอบ
				</Text>
				กรองสาขา
				<Box>
					<Select mb="2" onChange={(e) => {
						if (e.target.value) {
							setSelectFilter(e.target.value)
						}
					}}>
						<option value="all">ทั้งหมด</option>
						<option value="dmt">DMT</option>
						<option value="its">ITS</option>
						<option value="css">CSS</option>
					</Select>
				</Box>
				<Modal size="xl" onClose={() => (setModalContent(''), setTargetCollapse(''))} isOpen={!!modalContent} isCentered>
					<ModalOverlay />
					<ModalContent>
						<ModalHeader>ข้อมูลผู้เข้าสอบ</ModalHeader>
						<ModalCloseButton />
						<ModalBody>
							<Box bg="#e6e8ed" fontWeight="bold" textAlign="center" p="4" rounded="md" boxShadow="md">
								<Text>หมายเลขนักศึกษา: {displayData.studentId}</Text>
								<Text>ผู้สอบ: {displayData.studentName}</Text>
								<Text>สาขา: {displayData.studentBranch}</Text>
								<Text>จำนวนข้อทั้งหมด {
									displayData?.submits?.reduce((acc: number, item: any) => acc + JSON.parse(item.originalAnswers).length, 0)
								} ข้อ</Text>
								<Text>จำนวนข้อที่ทำได้ {
									displayData?.submits?.reduce((acc: number, item: any) => acc + JSON.parse(item.answers).filter((a: any) => a.answer == JSON.parse(item.originalAnswers).find((b: any) => b.id === a.questionId).answer).length, 0)
								} ข้อ</Text>
								<Text>คิดเป็น {
									Math.round(displayData?.submits?.reduce((acc: number, item: any) => acc + JSON.parse(item.answers).filter((a: any) => a.answer == JSON.parse(item.originalAnswers).find((b: any) => b.id === a.questionId)?.answer)?.length, 0) / displayData?.submits?.reduce((acc: number, item: any) => acc + JSON.parse(item.originalAnswers)?.length, 0) * 100)
								}%</Text>
							</Box>
							<Text mt="4" fontSize="xl" fontWeight="bold">
								รายการสอบ
							</Text>
							{
								displayData?.submits?.map((item: any, index: number) => (
									<Box key={index} mb="2">
										<Box onClick={() => targetCollapse == item.id ? setTargetCollapse('') : setTargetCollapse(item.id)} cursor="pointer" bg="green.400" color="white" textAlign="center" fontWeight="bold" p="4" rounded="md">
											{getExamNameById(item.examId) || 'ไม่มีชื่อแบบทดสอบ'} | {item.point} / {
												JSON.parse(item.originalAnswers).length
											} คะแนน
										</Box>
										<Collapse in={targetCollapse == item.id} animateOpacity>
											<AnswerCard data={item} />
										</Collapse>
									</Box>
								)
								)}
						</ModalBody>
						<ModalFooter>
							<Button onClick={() => (setModalContent(''), setTargetCollapse(''))}>ปิด</Button>
						</ModalFooter>
					</ModalContent>
				</Modal>
				<Box bg="white" p="4" rounded="md" boxShadow="md">
					<Table variant='simple'>
						<TableCaption>ทั้งหมด {history.length} ผู้ทำแบบทดสอบ</TableCaption>
						<Thead>
							<Tr>
								<Th>วันที่</Th>
								<Th>รหัสนักศึกษา</Th>
								<Th>ชื่อ</Th>
								<Th>สาขา</Th>
								<Th>คะแนน</Th>
								<Th>ตรวจสอบ</Th>
							</Tr>
						</Thead>
						<Tbody>
							{
								(selectFilter == 'all' ? history : history.filter((item: any) => item.studentBranch.toLowerCase() == selectFilter)).map((item: any, index) => (
									<Tr key={index}>
										<Td>{
											new Date(item.created_at).toLocaleString('th-TH', {
												year: 'numeric',
												month: 'long',
												day: 'numeric',
												hour: 'numeric',
												minute: 'numeric',
												second: 'numeric'
											})
										}</Td>
										<Td>{item.studentId}</Td>
										<Td>{item.studentName}</Td>
										<Td>{item.studentBranch}</Td>
										<Td>
											{
												//Total point
												item.submits.reduce((total: number, current: any) => {
													return total + current.point;
												}
													, 0)
											} / {
												//Total question
												item.submits.reduce((total: number, current: any) => {
													return total + JSON.parse(current.originalAnswers).length;
												}
													, 0)
											}
										</Td>
										<Td>
											<IconButton onClick={() => setModalContent(item.id)} colorScheme="blue" aria-label="ตรวจสอบ" mr="2" icon={<BiSearch />} />
											<Popover >
												<PopoverTrigger>
													<IconButton aria-label="ลบข้อมูลนี้" colorScheme="red" icon={<BiTrash />} />
												</PopoverTrigger>
												<PopoverContent textAlign="left">
													<PopoverHeader fontWeight='semibold'>ลบ</PopoverHeader>
													<PopoverArrow />
													<PopoverCloseButton />
													<PopoverBody>
														คุณต้องการข้อมูลนี้ใช่หรือไม่
													</PopoverBody>
													<PopoverFooter display='flex' justifyContent='flex-end'>
														<ButtonGroup size='sm'>
															<Button isLoading={isLoading} onClick={() => onDelelteButtonClick(item.id)} colorScheme='red'>ลบ</Button>
														</ButtonGroup>
													</PopoverFooter>
												</PopoverContent>
											</Popover>
										</Td>
									</Tr>
								))
							}
						</Tbody>
					</Table>
				</Box>
			</Box>
		</Sidebar>
	);
}
export default TesterPage;
